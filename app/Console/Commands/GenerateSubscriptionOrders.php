<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\TrxSubscription;
use App\Services\XenditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateSubscriptionOrders extends Command
{
    protected $signature = 'subscriptions:generate-orders';

    protected $description = 'Create today\'s orders for every active recurring ("rutin") subscription due today';

    /** Carbon::dayOfWeekIso (1=Monday..7=Sunday) => Indonesian day name, matching MdxArea::getActiveDaysAttribute() and the cart's routine-day picker. */
    private const ISO_DAY_NAMES = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
        5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
    ];

    public function handle(XenditService $xendit): int
    {
        $today = now()->toDateString();
        $todayName = self::ISO_DAY_NAMES[now()->dayOfWeekIso];

        $due = TrxSubscription::with(['customer', 'product'])
            ->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('last_generated_date')->orWhere('last_generated_date', '<', $today);
            })
            ->get()
            ->filter(fn (TrxSubscription $s) => $s->isDueOn($todayName));

        if ($due->isEmpty()) {
            $this->info("No subscriptions due on {$todayName}.");
            return self::SUCCESS;
        }

        // Group by customer + shipping_address + payment_method, so a customer
        // with several subscribed products due the same day gets one combined
        // order instead of one per product — mirrors a normal manual checkout.
        $groups = $due->groupBy(fn (TrxSubscription $s) => $s->customer_id . '|' . $s->shipping_address . '|' . $s->payment_method);

        foreach ($groups as $subscriptions) {
            $this->generateOrderForGroup($subscriptions, $today, $xendit);
        }

        return self::SUCCESS;
    }

    private function generateOrderForGroup($subscriptions, string $today, XenditService $xendit): void
    {
        $first = $subscriptions->first();
        $customer = $first->customer;
        $isCod = $first->payment_method === 'COD';

        if (!$customer) {
            Log::warning('Subscription order generation skipped: customer not found', ['subscription_id' => $first->id]);
            return;
        }

        if ($isCod && !optional($customer->customerProfile)->is_verified) {
            Log::warning('Subscription order generation skipped: customer no longer COD-verified', ['customer_id' => $customer->id]);
            return;
        }

        $order = null;

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalDiscount = 0;
            $itemCount = 0;

            $lastOrder = TrxOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -5)) + 1 : 1;
            $orderNumber = 'INV-' . now()->format('Ymd') . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            $order = TrxOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_address' => $first->shipping_address,
                'payment_method' => $first->payment_method,
                'total_amount' => 0,
                'total_discount' => 0,
                'status' => OrderStatusEnum::ON_PROCESS,
                'payment_status' => 'UNPAID',
                'source' => 'subscription',
            ]);

            foreach ($subscriptions as $subscription) {
                $product = $subscription->product;

                if (!$product || $product->stock < $subscription->quantity) {
                    Log::warning('Subscription line skipped: product missing or out of stock', [
                        'subscription_id' => $subscription->id,
                        'product_id' => $subscription->product_id,
                    ]);
                    continue;
                }

                $originalPrice = $product->price;

                ['price' => $discountPrice, 'discount' => $activeDiscount] = $product->priceForQuantity((int) $subscription->quantity);
                $discountAmount = 0;
                $discountId = null;

                if ($activeDiscount) {
                    $discountId = $activeDiscount->id;
                    $discountAmount = ($originalPrice - $discountPrice) * $subscription->quantity;
                }

                $subtotal = $discountPrice * $subscription->quantity;

                TrxOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'original_price' => $originalPrice,
                    'quantity' => $subscription->quantity,
                    'price' => $discountPrice,
                    'discount_amount' => $discountAmount,
                    'discount_id' => $discountId,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
                $totalDiscount += $discountAmount;
                $itemCount++;

                $product->decrement('stock', $subscription->quantity);
                $subscription->update(['last_generated_date' => $today]);
            }

            if ($itemCount === 0) {
                // Every line was out of stock / invalid — discard the empty order.
                DB::rollBack();
                return;
            }

            $order->update(['total_amount' => $totalAmount, 'total_discount' => $totalDiscount]);

            $order->invoice()->create([
                'invoice_number' => $order->order_number,
                'issue_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'UNPAID',
                'total_amount' => $order->total_amount,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Subscription order generation failed', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
            return;
        }

        $this->info("Order {$order->order_number} created for customer #{$customer->id} ({$itemCount} item(s)).");

        if ($isCod) {
            return;
        }

        // Same non-fatal pattern as the manual checkout flow: the order is
        // already committed, so a payment-creation failure here just leaves
        // it unpaid for the customer to retry from their order page.
        try {
            $xendit->createPayment($order, $order->payment_method);
        } catch (\Throwable $e) {
            Log::error('Subscription Xendit createPayment failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }
}
