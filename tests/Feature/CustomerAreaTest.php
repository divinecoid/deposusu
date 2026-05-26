<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MdxArea;
use App\Models\MdxBranch;
use App\Models\MdxCustomer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAreaTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_belongs_to_area_relationship(): void
    {
        // Create branch first
        $branch = MdxBranch::create([
            'name' => 'Branch Utama',
            'code' => 'BR-001',
        ]);

        // Create area
        $area = MdxArea::create([
            'name' => 'Area Jakarta Selatan',
            'code' => 'JKT-SEL',
            'branch_id' => $branch->id,
        ]);

        // Create customer user
        $user = User::factory()->create(['role' => 'customer']);

        // Create customer profile
        $customer = MdxCustomer::create([
            'user_id' => $user->id,
            'phone' => '08123456789',
            'address' => 'Jl. Kemang Raya No. 10',
            'area_id' => $area->id,
        ]);

        // Test relationships
        $this->assertEquals($area->id, $customer->area->id);
        $this->assertEquals('Area Jakarta Selatan', $customer->area->name);

        $this->assertCount(1, $area->customers);
        $this->assertEquals($customer->id, $area->customers->first()->id);
    }

    public function test_admin_can_assign_area_to_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Create branch and area
        $branch = MdxBranch::create([
            'name' => 'Branch Utama',
            'code' => 'BR-001',
        ]);
        $area = MdxArea::create([
            'name' => 'Area Jakarta Selatan',
            'code' => 'JKT-SEL',
            'branch_id' => $branch->id,
        ]);

        // Create customer user
        $customerUser = User::factory()->create(['role' => 'customer']);

        // Assign area via controller update
        $postData = [
            'name' => 'Customer Updated',
            'email' => $customerUser->email,
            'phone' => '08111222333',
            'address' => 'Alamat baru customer',
            'area_id' => $area->id,
        ];

        $response = $this->put(route('admin.master.customers.update', $customerUser->id), $postData);
        $response->assertRedirect();

        // Refresh and check profile
        $profile = $customerUser->fresh()->customerProfile;
        $this->assertNotNull($profile);
        $this->assertEquals($area->id, $profile->area_id);
        $this->assertEquals('08111222333', $profile->phone);
        $this->assertEquals('Alamat baru customer', $profile->address);
    }
}
