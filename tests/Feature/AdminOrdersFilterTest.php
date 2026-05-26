<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TrxOrder;
use App\Enums\OrderStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrdersFilterTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $order1;
    private $order2;
    private $order3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);

        // Order 1: customer "Budi Santoso", source "customer", created on 2026-05-20
        $this->order1 = TrxOrder::create([
            'order_number' => 'INV-20260520-00001',
            'customer_name' => 'Budi Santoso',
            'total_amount' => 20000,
            'total_discount' => 0,
            'status' => OrderStatusEnum::PENDING,
            'payment_status' => 'UNPAID',
            'source' => 'customer',
        ]);
        $this->order1->created_at = '2026-05-20 10:00:00';
        $this->order1->save();

        // Order 2: customer "Siti Aminah", source "admin", created on 2026-05-22
        $this->order2 = TrxOrder::create([
            'order_number' => 'INV-20260522-00001',
            'customer_name' => 'Siti Aminah',
            'total_amount' => 35000,
            'total_discount' => 0,
            'status' => OrderStatusEnum::ON_PROCESS,
            'payment_status' => 'UNPAID',
            'source' => 'admin',
        ]);
        $this->order2->created_at = '2026-05-22 14:00:00';
        $this->order2->save();

        // Order 3: customer "Siti Aisyah", source "customer", created on 2026-05-24
        $this->order3 = TrxOrder::create([
            'order_number' => 'INV-20260524-00001',
            'customer_name' => 'Siti Aisyah',
            'total_amount' => 50000,
            'total_discount' => 0,
            'status' => OrderStatusEnum::DONE,
            'payment_status' => 'PAID',
            'source' => 'customer',
        ]);
        $this->order3->created_at = '2026-05-24 16:00:00';
        $this->order3->save();
    }

    public function test_admin_can_filter_orders_by_customer_name(): void
    {
        // 1. Search for "Siti"
        $response = $this->get(route('admin.orders.index', ['customer' => 'Siti']));
        $response->assertStatus(200);

        // Should see Order 2 and Order 3
        $response->assertSee('Siti Aminah');
        $response->assertSee('Siti Aisyah');
        // Should NOT see Order 1
        $response->assertDontSee('Budi Santoso');

        // 2. Search for "Budi"
        $response = $this->get(route('admin.orders.index', ['customer' => 'Budi']));
        $response->assertStatus(200);

        // Should see Order 1
        $response->assertSee('Budi Santoso');
        // Should NOT see Order 2 and 3
        $response->assertDontSee('Siti Aminah');
        $response->assertDontSee('Siti Aisyah');
    }

    public function test_admin_can_filter_orders_by_date_range(): void
    {
        // 1. Filter dates between 2026-05-21 and 2026-05-23
        $response = $this->get(route('admin.orders.index', [
            'start_date' => '2026-05-21',
            'end_date' => '2026-05-23',
        ]));
        $response->assertStatus(200);

        // Should see Order 2 (2026-05-22)
        $response->assertSee('Siti Aminah');
        // Should NOT see Order 1 (2026-05-20) or Order 3 (2026-05-24)
        $response->assertDontSee('Budi Santoso');
        $response->assertDontSee('Siti Aisyah');

        // 2. Filter dates between 2026-05-23 and 2026-05-25
        $response = $this->get(route('admin.orders.index', [
            'start_date' => '2026-05-23',
            'end_date' => '2026-05-25',
        ]));
        $response->assertStatus(200);

        // Should see Order 3 (2026-05-24)
        $response->assertSee('Siti Aisyah');
        // Should NOT see Order 1 or Order 2
        $response->assertDontSee('Budi Santoso');
        $response->assertDontSee('Siti Aminah');
    }

    public function test_admin_can_filter_orders_by_source(): void
    {
        // 1. Filter source "admin"
        $response = $this->get(route('admin.orders.index', ['source' => 'admin']));
        $response->assertStatus(200);

        // Should see Order 2 (Admin Toko)
        $response->assertSee('Siti Aminah');
        $response->assertSee('Admin Toko');
        // Should NOT see Order 1 or Order 3 (Customer app source)
        $response->assertDontSee('Budi Santoso');
        $response->assertDontSee('Siti Aisyah');

        // 2. Filter source "customer"
        $response = $this->get(route('admin.orders.index', ['source' => 'customer']));
        $response->assertStatus(200);

        // Should see Order 1 and Order 3
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Aisyah');
        // Should NOT see Order 2
        $response->assertDontSee('Siti Aminah');
    }
}
