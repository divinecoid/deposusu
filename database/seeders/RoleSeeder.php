<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $roles = [
            'Super Admin',
            'Owner',
            'Admin Operasional',
            'Staff Gudang',
            'Kasir',
            'Finance'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Migrate existing users to 'Super Admin' if they are admin, or map appropriately.
        // For now, let's assign Super Admin to the main test account
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin && !$admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }

        $cashier = User::where('email', 'cashier@example.com')->first();
        if ($cashier && !$cashier->hasRole('Kasir')) {
            $cashier->assignRole('Kasir');
        }

        // Create generic testing users if they don't exist
        $testUsers = [
            'owner@example.com' => 'Owner',
            'adminops@example.com' => 'Admin Operasional',
            'gudang@example.com' => 'Staff Gudang',
            'finance@example.com' => 'Finance',
        ];

        foreach ($testUsers as $email => $role) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $role . ' User',
                    'password' => bcrypt('password'),
                    'role' => strtolower(str_replace(' ', '', $role)), // backward compatibility with hardcoded role column
                ]
            );
            
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
