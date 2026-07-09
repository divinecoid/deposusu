<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MdxDriver;
use App\Models\MdxCustomer;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin
        User::updateOrCreate(
            ['email' => 'admin@deposusu.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Cashier (Kasir)
        User::updateOrCreate(
            ['email' => 'kasir@deposusu.com'],
            [
                'name' => 'Kasir Utama',
                'password' => 'password',
                'role' => 'cashier',
                'email_verified_at' => now(),
            ]
        );

        // 3. Warehouse (Gudang)
        User::updateOrCreate(
            ['email' => 'gudang@deposusu.com'],
            [
                'name' => 'Staf Gudang',
                'password' => 'password',
                'role' => 'preparist', // Adjusted to match the isPreparist() logic
                'email_verified_at' => now(),
            ]
        );

        // 4. Drivers
        $driver1 = User::updateOrCreate(
            ['email' => 'driver1@deposusu.com'],
            [
                'name' => 'Budi Driver',
                'phone' => '081234567891',
                'password' => 'password',
                'role' => 'driver',
                'email_verified_at' => now(),
            ]
        );
        MdxDriver::updateOrCreate(
            ['user_id' => $driver1->id],
            ['license_plate' => 'B 1234 CD', 'vehicle_type' => 'Grand Max Box']
        );

        $driver2 = User::updateOrCreate(
            ['email' => 'driver2@deposusu.com'],
            [
                'name' => 'Anto Driver',
                'phone' => '081234567892',
                'password' => 'password',
                'role' => 'driver',
                'email_verified_at' => now(),
            ]
        );
        MdxDriver::updateOrCreate(
            ['user_id' => $driver2->id],
            ['license_plate' => 'B 5678 EF', 'vehicle_type' => 'L300']
        );

        // Demo Driver (matching login page defaults)
        $driverDemo = User::updateOrCreate(
            ['email' => 'driver@deposusu.com'],
            [
                'name' => 'Budi Kurir Demo',
                'phone' => '081234567890',
                'password' => 'password123',
                'role' => 'driver',
                'email_verified_at' => now(),
            ]
        );
        MdxDriver::updateOrCreate(
            ['user_id' => $driverDemo->id],
            ['license_plate' => 'B 9999 DD', 'vehicle_type' => 'Suzuki Carry Box']
        );

        // 5. Customers
        $customer1 = User::updateOrCreate(
            ['email' => 'customer1@deposusu.com'],
            [
                'name' => 'Siti Customer',
                'password' => 'password',
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );
        MdxCustomer::updateOrCreate(
            ['user_id' => $customer1->id],
            ['phone' => '081234567890', 'address' => 'Jl. Mawar No. 1, Jakarta']
        );

        $customer2 = User::updateOrCreate(
            ['email' => 'customer2@deposusu.com'],
            [
                'name' => 'Rudi Pelanggan',
                'password' => 'password',
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );
        MdxCustomer::updateOrCreate(
            ['user_id' => $customer2->id],
            ['phone' => '089876543210', 'address' => 'Jl. Melati No. 5, Bogor']
        );

        $this->command->info('Users seeded successfully!');
    }
}
