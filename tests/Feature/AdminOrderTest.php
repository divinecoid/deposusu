<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TrxOrder;
use App\Models\MdxProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_order_creation_page(): void
    {
        $response = $this->get(route('admin.sales-order.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_can_access_order_creation_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.sales-order.create'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_order_manually_and_deducts_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Create a customer
        $customer = User::factory()->create(['role' => 'customer']);

        // Create products
        $product1 = MdxProduct::create([
            'name' => 'Susu Coklat UHT',
            'sku' => 'SS-COK-001',
            'price' => 10000.00,
            'stock' => 20,
            'low_stock_threshold' => 5,
        ]);

        $product2 = MdxProduct::create([
            'name' => 'Susu Strawberry UHT',
            'sku' => 'SS-STR-002',
            'price' => 12000.00,
            'stock' => 15,
            'low_stock_threshold' => 5,
        ]);

        $postData = [
            'customer_id' => $customer->id,
            'products' => [
                [
                    'id' => $product1->id,
                    'quantity' => 2,
                ],
                [
                    'id' => $product2->id,
                    'quantity' => 3,
                ]
            ]
        ];

        $response = $this->post(route('admin.sales-order.store'), $postData);

        // Assert redirect to order show page
        $order = TrxOrder::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('admin.orders.show', $order->id));

        // Assert order source and details
        $this->assertEquals('admin', $order->source);
        $this->assertEquals($customer->name, $order->customer_name);
        $this->assertEquals(56000.00, $order->total_amount); // (10000*2) + (12000*3) = 20000 + 36000 = 56000

        // Assert order items
        $this->assertCount(2, $order->items);

        // Assert stock levels decremented correctly
        $this->assertEquals(18, $product1->fresh()->stock); // 20 - 2 = 18
        $this->assertEquals(12, $product2->fresh()->stock); // 15 - 3 = 12
    }
}
