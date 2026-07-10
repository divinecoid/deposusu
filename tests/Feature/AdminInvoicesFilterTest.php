<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TrxOrder;
use App\Models\TrxInvoice;
use App\Models\MdxProduct;
use App\Models\TrxCart;
use App\Models\TrxCartItem;
use App\Enums\OrderStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInvoicesFilterTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $customer;
    private $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);

        $this->product = MdxProduct::create([
            'name' => 'Susu Strawberry UHT',
            'sku' => 'SS-STR-002',
            'price' => 12000.00,
            'stock' => 50,
            'low_stock_threshold' => 5,
        ]);
    }

    public function test_admin_manual_order_creation_immediately_generates_invoice(): void
    {
        $this->actingAs($this->admin);

        $postData = [
            'customer_id' => $this->customer->id,
            'products' => [
                [
                    'id' => $this->product->id,
                    'quantity' => 3, // Total: 36000
                ]
            ]
        ];

        // Ensure no invoices exist initially
        $this->assertEquals(0, TrxInvoice::count());

        $response = $this->post(route('admin.sales-order.store'), $postData);

        // Assert order was created
        $order = TrxOrder::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('admin.orders.show', $order->id));

        // Assert invoice was immediately generated with matching totals
        $this->assertEquals(1, TrxInvoice::count());
        $invoice = TrxInvoice::first();
        $this->assertEquals($order->id, $invoice->order_id);
        $this->assertEquals('INV-' . $order->order_number, $invoice->invoice_number);
        $this->assertEquals(36000.00, $invoice->total_amount);
        $this->assertEquals('UNPAID', $invoice->status);
    }

    public function test_customer_checkout_immediately_generates_invoice(): void
    {
        $this->actingAs($this->customer);

        // Put item in customer's cart
        $cart = TrxCart::create(['user_id' => $this->customer->id]);
        TrxCartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2, // Total: 24000
            'price' => 12000.00,
        ]);

        // Ensure no invoices exist initially
        $this->assertEquals(0, TrxInvoice::count());

        $response = $this->postJson(route('cart.checkout'));
        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);

        // Assert invoice was immediately generated
        $this->assertEquals(1, TrxInvoice::count());
        $invoice = TrxInvoice::first();
        $order = TrxOrder::first();
        $this->assertNotNull($order);
        $this->assertEquals($order->id, $invoice->order_id);
        $this->assertEquals(24000.00, $invoice->total_amount);
        $this->assertEquals('UNPAID', $invoice->status);
    }

    public function test_admin_can_filter_invoices_by_delivery_status(): void
    {
        $this->actingAs($this->admin);

        // Order 1: status DONE (Shipped)
        $order1 = TrxOrder::create([
            'order_number' => 'INV-20260520-00001',
            'customer_name' => 'Budi',
            'total_amount' => 10000,
            'total_discount' => 0,
            'status' => OrderStatusEnum::DONE,
            'payment_status' => 'PAID',
        ]);
        $invoice1 = TrxInvoice::create([
            'invoice_number' => 'INV-INV-20260520-00001',
            'order_id' => $order1->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'PAID',
            'total_amount' => 10000,
        ]);

        // Order 2: status ON_PROCESS (Pending delivery)
        $order2 = TrxOrder::create([
            'order_number' => 'INV-20260521-00001',
            'customer_name' => 'Siti',
            'total_amount' => 20000,
            'total_discount' => 0,
            'status' => OrderStatusEnum::ON_PROCESS,
            'payment_status' => 'UNPAID',
        ]);
        $invoice2 = TrxInvoice::create([
            'invoice_number' => 'INV-INV-20260521-00001',
            'order_id' => $order2->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'UNPAID',
            'total_amount' => 20000,
        ]);

        // Order 3: status DELIVERED (Shipped)
        $order3 = TrxOrder::create([
            'order_number' => 'INV-20260522-00001',
            'customer_name' => 'Andi',
            'total_amount' => 30000,
            'total_discount' => 0,
            'status' => OrderStatusEnum::DELIVERED,
            'payment_status' => 'PAID',
        ]);
        $invoice3 = TrxInvoice::create([
            'invoice_number' => 'INV-INV-20260522-00001',
            'order_id' => $order3->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'PAID',
            'total_amount' => 30000,
        ]);

        // 1. Get shipped invoices
        $response = $this->get(route('admin.invoices.index', ['delivery_status' => 'shipped']));
        $response->assertStatus(200);

        // Should see Order 1 and Order 3
        $response->assertSee('Budi');
        $response->assertSee('Andi');
        // Should NOT see Order 2
        $response->assertDontSee('Siti');

        // 2. Get pending delivery invoices
        $response = $this->get(route('admin.invoices.index', ['delivery_status' => 'pending']));
        $response->assertStatus(200);

        // Should see Order 2
        $response->assertSee('Siti');
        // Should NOT see Order 1 and Order 3
        $response->assertDontSee('Budi');
        $response->assertDontSee('Andi');
    }
}
