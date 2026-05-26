<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MdxArea;
use App\Models\MdxBranch;
use App\Models\MdxCustomer;
use App\Models\MdxProduct;
use App\Models\TrxCart;
use App\Models\TrxCartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAreaDaysTest extends TestCase
{
    use RefreshDatabase;

    private $branch;
    private $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = MdxBranch::create([
            'name' => 'Branch Utama',
            'code' => 'BR-001',
        ]);

        $this->product = MdxProduct::create([
            'name' => 'Susu Murni UHT',
            'code' => 'PRD-001',
            'price' => 15000,
            'stock' => 100,
            'description' => 'Susu segar bergizi tinggi',
        ]);
    }

    public function test_guest_cannot_see_routine_toggle_and_is_blocked_in_api(): void
    {
        // Setup a guest session cart
        $cart = TrxCart::create([
            'session_id' => 'dummy-session-id'
        ]);
        $cartItem = TrxCartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 15000,
        ]);

        // 1. Guest visits /cart
        // Emulate session
        $response = $this->withSession(['_token' => 'dummy-csrf-token'])
            ->get(route('cart.index'));

        $response->assertStatus(200);
        // Assert that the word "Rutin" toggle checkbox is not rendered
        $response->assertDontSee('type="checkbox" value="" class="sr-only peer"');
        $response->assertDontSee('Rutin');

        // 2. Guest tries to PATCH update-routine route directly
        $patchResponse = $this->patchJson(route('cart.update-routine', $cartItem->id), [
            'is_routine' => true,
            'routine_schedule' => ['Senin']
        ]);

        $patchResponse->assertStatus(401);
        $patchResponse->assertJsonFragment([
            'success' => false,
            'message' => 'Silakan login terlebih dahulu untuk menggunakan fitur rutin'
        ]);
    }

    public function test_logged_in_customer_with_area_days_only_sees_active_days(): void
    {
        // 1. Create area with specific active days (only Senin and Kamis)
        $area = MdxArea::create([
            'name' => 'Area Depok',
            'code' => 'DPK-01',
            'branch_id' => $this->branch->id,
            'is_monday' => true,      // Senin (Sen)
            'is_tuesday' => false,
            'is_wednesday' => false,
            'is_thursday' => true,    // Kamis (Kam)
            'is_friday' => false,
            'is_saturday' => false,
            'is_sunday' => false,
        ]);

        // 2. Create customer user and profile linked to that area
        $customerUser = User::factory()->create(['role' => 'customer']);
        MdxCustomer::create([
            'user_id' => $customerUser->id,
            'phone' => '08123456789',
            'address' => 'Depok Raya No. 5',
            'area_id' => $area->id,
        ]);

        // 3. Setup cart and item for this user
        $cart = TrxCart::create([
            'user_id' => $customerUser->id
        ]);
        TrxCartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 15000,
        ]);

        $this->actingAs($customerUser);

        // 4. Visit cart page
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);

        // Should see the Routine option
        $response->assertSee('Rutin');

        // Should render active days: Sen, Kam
        $response->assertSee('Sen');
        $response->assertSee('Kam');

        // Should NOT render disabled days
        $response->assertDontSee('day-btn-' . $cart->items->first()->id . '-Selasa');
        $response->assertDontSee('day-btn-' . $cart->items->first()->id . '-Rabu');
        $response->assertDontSee('day-btn-' . $cart->items->first()->id . '-Jumat');
        $response->assertDontSee('day-btn-' . $cart->items->first()->id . '-Sabtu');
        $response->assertDontSee('day-btn-' . $cart->items->first()->id . '-Minggu');
    }

    public function test_logged_in_customer_with_no_area_defaults_to_all_seven_days(): void
    {
        // 1. Create customer user and profile with NULL area
        $customerUser = User::factory()->create(['role' => 'customer']);
        MdxCustomer::create([
            'user_id' => $customerUser->id,
            'phone' => '08123456789',
            'address' => 'Depok Raya No. 5',
            'area_id' => null, // No area
        ]);

        // 2. Setup cart and item for this user
        $cart = TrxCart::create([
            'user_id' => $customerUser->id
        ]);
        TrxCartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 15000,
        ]);

        $this->actingAs($customerUser);

        // 3. Visit cart page
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);

        // Should render all 7 days as active/fallback
        $response->assertSee('Sen');
        $response->assertSee('Sel');
        $response->assertSee('Rab');
        $response->assertSee('Kam');
        $response->assertSee('Jum');
        $response->assertSee('Sab');
        $response->assertSee('Min');
    }
}
