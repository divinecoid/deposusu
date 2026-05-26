<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MdxArea;
use App\Models\MdxBranch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaActiveDaysTest extends TestCase
{
    use RefreshDatabase;

    public function test_area_active_days_indonesian_getter(): void
    {
        $branch = MdxBranch::create([
            'name' => 'Branch Utama',
            'code' => 'BR-001',
        ]);

        $area = MdxArea::create([
            'name' => 'Area Depok',
            'code' => 'DPK-01',
            'branch_id' => $branch->id,
            'is_monday' => true,
            'is_tuesday' => true,
            'is_wednesday' => false,
            'is_thursday' => true,
            'is_friday' => false,
            'is_saturday' => false,
            'is_sunday' => false,
        ]);

        $expectedDays = ['Senin', 'Selasa', 'Kamis'];
        $this->assertEquals($expectedDays, $area->active_days);
    }

    public function test_admin_can_create_area_with_active_days(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $branch = MdxBranch::create([
            'name' => 'Branch Utama',
            'code' => 'BR-001',
        ]);

        $postData = [
            'name' => 'Area Bekasi',
            'code' => 'BKS-01',
            'branch_id' => $branch->id,
            'is_monday' => '1',
            'is_wednesday' => '1',
            'is_friday' => '1',
        ];

        $response = $this->post(route('admin.master.areas.store'), $postData);
        $response->assertRedirect();

        $area = MdxArea::where('code', 'BKS-01')->first();
        $this->assertNotNull($area);
        $this->assertTrue($area->is_monday);
        $this->assertFalse($area->is_tuesday); // not passed, should default to false in controller mapping
        $this->assertTrue($area->is_wednesday);
        $this->assertFalse($area->is_thursday);
        $this->assertTrue($area->is_friday);
        $this->assertFalse($area->is_saturday);
        $this->assertFalse($area->is_sunday);

        $this->assertEquals(['Senin', 'Rabu', 'Jumat'], $area->active_days);
    }

    public function test_admin_can_update_area_active_days_and_uncheck_them(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $branch = MdxBranch::create([
            'name' => 'Branch Utama',
            'code' => 'BR-001',
        ]);

        // Starts with all days true
        $area = MdxArea::create([
            'name' => 'Area Depok',
            'code' => 'DPK-01',
            'branch_id' => $branch->id,
            'is_monday' => true,
            'is_tuesday' => true,
            'is_wednesday' => true,
            'is_thursday' => true,
            'is_friday' => true,
            'is_saturday' => true,
            'is_sunday' => true,
        ]);

        // Uncheck everything except Tuesday and Saturday
        $updateData = [
            'name' => 'Area Depok Baru',
            'code' => 'DPK-01',
            'branch_id' => $branch->id,
            'is_tuesday' => '1',
            'is_saturday' => '1',
        ];

        $response = $this->put(route('admin.master.areas.update', $area->id), $updateData);
        $response->assertRedirect();

        $area = $area->fresh();
        $this->assertFalse($area->is_monday);
        $this->assertTrue($area->is_tuesday);
        $this->assertFalse($area->is_wednesday);
        $this->assertFalse($area->is_thursday);
        $this->assertFalse($area->is_friday);
        $this->assertTrue($area->is_saturday);
        $this->assertFalse($area->is_sunday);

        $this->assertEquals(['Selasa', 'Sabtu'], $area->active_days);
    }
}
