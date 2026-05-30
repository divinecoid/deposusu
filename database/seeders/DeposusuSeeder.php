<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MdxProduct;

class DeposusuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Users
        
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@deposusu.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Preparist (Packer)
        User::updateOrCreate(
            ['email' => 'preparist@deposusu.com'],
            [
                'name' => 'Andi Preparist',
                'password' => Hash::make('password123'),
                'role' => 'preparist',
            ]
        );

        // Driver
        User::updateOrCreate(
            ['email' => 'driver@deposusu.com'],
            [
                'name' => 'Budi Driver',
                'password' => Hash::make('password123'),
                'role' => 'driver',
            ]
        );

        // Customer
        User::updateOrCreate(
            ['email' => 'customer@deposusu.com'],
            [
                'name' => 'Citra Customer',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        // 2. Seed Products
        $products = [
            [
                'name' => 'Susu Kambing Botol',
                'sku' => 'SKU-001',
                'barcode' => '8991234567890',
                'price' => 25000,
                'description' => 'Susu kambing murni segar 250ml',
                'image' => null,
                'stock' => 100,
                'low_stock_threshold' => 20,
            ],
            [
                'name' => 'Susu Sapi Murni',
                'sku' => 'SKU-002',
                'barcode' => '8991234567891',
                'price' => 100000,
                'description' => 'Susu sapi murni segar 1 Liter',
                'image' => null,
                'stock' => 50,
                'low_stock_threshold' => 10,
            ],
            [
                'name' => 'Kefir Susu Sapi',
                'sku' => 'SKU-003',
                'barcode' => '8991234567892',
                'price' => 25000,
                'description' => 'Kefir fermentasi susu sapi murni',
                'image' => null,
                'stock' => 200,
                'low_stock_threshold' => 50,
            ],
            [
                'name' => 'Yogurt Strawberry',
                'sku' => 'SKU-004',
                'barcode' => '8991234567893',
                'price' => 50000,
                'description' => 'Yogurt rasa buah stroberi asli',
                'image' => null,
                'stock' => 80,
                'low_stock_threshold' => 15,
            ]
        ];

        foreach ($products as $product) {
            MdxProduct::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
