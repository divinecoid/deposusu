<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixPreparistRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Perbaiki role untuk akun gudang jika sudah ada
        User::where('email', 'gudang@deposusu.com')->update(['role' => 'preparist']);

        // 2. Buat akun preparist baru untuk jaga-jaga
        User::updateOrCreate(
            ['email' => 'preparist@deposusu.com'],
            [
                'name' => 'Andi Preparist',
                'password' => Hash::make('password'),
                'role' => 'preparist',
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info('Role preparist berhasil diperbaiki!');
    }
}
