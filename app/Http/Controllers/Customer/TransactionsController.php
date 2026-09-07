<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\TrxCart;
use App\Services\XenditService;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'ongoing');
        
        $query = TrxOrder::with(['items.product', 'invoice'])
            ->where('customer_name', auth()->user()->name);

        switch ($tab) {
            case 'completed':
                $query->whereIn('status', ['done']);
                break;
            case 'cancelled':
                $query->whereIn('status', ['cancelled', 'rejected']);
                break;
            case 'ongoing':
            default:
                $query->whereNotIn('status', ['done', 'cancelled', 'rejected']);
                $tab = 'ongoing';
                break;
        }

        $orders = $query->latest()->paginate(10)->appends(['tab' => $tab]);

        return view('customer.transactions.index', compact('orders', 'tab'));
    }

    public function show(TrxOrder $order, XenditService $xendit)
    {
        $ownsByName = $order->customer_name === auth()->user()->name;
        $ownsById = $order->customer_id && (int) $order->customer_id === (int) auth()->id();

        if (!$ownsByName && !$ownsById) {
            abort(404);
        }

        $order->load(['items.product', 'invoice', 'driver', 'warehouse', 'latestXenditPayment']);

        $paymentChannel = $xendit->channel($order->payment_method);
        $canRetryPayment = $order->payment_status !== 'PAID'
            && $order->payment_method !== 'COD'
            && $paymentChannel !== null;

        return view('customer.transactions.show', compact('order', 'paymentChannel', 'canRetryPayment'));
    }

    public function reorder(TrxOrder $order)
    {
        if ($order->customer_name !== auth()->user()->name) {
            abort(404);
        }

        $cart = TrxCart::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        $addedCount = 0;
        $outOfStockItems = [];

        foreach ($order->items as $item) {
            $product = $item->product;
            
            if ($product && $product->stock > 0) {
                // If requested quantity is more than stock, add whatever is available
                $quantityToAdd = min($item->quantity, $product->stock);
                $cart->addItem($product->id, $quantityToAdd);
                $addedCount++;
            } else {
                $outOfStockItems[] = $product->name;
            }
        }

        if ($addedCount > 0) {
            $message = 'Berhasil memasukkan kembali barang ke keranjang!';
            if (count($outOfStockItems) > 0) {
                $message .= ' Beberapa barang sedang kosong: ' . implode(', ', $outOfStockItems);
            }
            return redirect()->route('cart.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Semua barang dalam pesanan ini sedang kosong.');
        }
    }

    public function invoice(TrxOrder $order)
    {
        if ($order->customer_name !== auth()->user()->name) {
            abort(404);
        }

        $order->load(['items.product', 'invoice', 'driver', 'warehouse']);

        return view('customer.transactions.invoice', compact('order'));
    }

    public function logPrint(Request $request, TrxOrder $order)
    {
        if ($order->customer_name !== auth()->user()->name) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $invoice = $order->invoice;
        if ($invoice) {
            $invoice->increment('print_count');
            $invoice->update([
                'last_printed_at' => now(),
                'last_printed_by_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'print_count' => $invoice->print_count,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
    }
}
