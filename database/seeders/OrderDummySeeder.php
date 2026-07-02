<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\MdxCustomer;
use App\Models\MdxWarehouse;
use App\Models\MdxProduct;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class OrderDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Products and Warehouses are seeded first
        if (MdxWarehouse::count() === 0) {
            $this->call(WarehouseDummySeeder::class);
        }
        if (MdxProduct::count() === 0) {
            $this->call(DeposusuSeeder::class);
        }

        $warehouse = MdxWarehouse::first() ?? MdxWarehouse::create([
            'name' => 'Gudang Utama Deposusu',
            'address' => 'Jl. Raya Utama No. 10, Jakarta Selatan'
        ]);

        // Find or seed our demo driver Budi Kurir Demo
        $driver = User::where('email', 'driver@deposusu.com')->first();
        if (!$driver) {
            $driver = User::create([
                'name' => 'Budi Kurir Demo',
                'email' => 'driver@deposusu.com',
                'phone' => '081234567890',
                'password' => Hash::make('password123'),
                'role' => 'driver',
                'email_verified_at' => now(),
            ]);
            $driver->driverProfile()->create([
                'license_plate' => 'B 9999 DD',
                'vehicle_type' => 'Suzuki Carry Box'
            ]);
        }

        // 2. Define Customer details to seed
        $customerData = [
            'ahmad' => [
                'name' => 'Ahmad Pelanggan (DepoSusu App)',
                'email' => 'customer_ahmad@deposusu.com',
                'phone' => '08111222333',
                'address' => 'Jl. Sudirman No. 10, Senayan',
            ],
            'siti' => [
                'name' => 'Siti Rahma (Shopee)',
                'email' => 'customer_siti@deposusu.com',
                'phone' => '08999888777',
                'address' => 'Jl. H. R. Rasuna Said Kav. X-5, Kuningan',
            ],
            'danu' => [
                'name' => 'Mas Danu (Tokopedia)',
                'email' => 'customer_danu@deposusu.com',
                'phone' => '081234567890',
                'address' => 'Jl. Fatmawati Raya No. 88, Cilandak',
            ],
            'dita' => [
                'name' => 'Mbak Dita (TikTok Shop)',
                'email' => 'customer_dita@deposusu.com',
                'phone' => '081234567895',
                'address' => 'Jl. Wijaya Timur No. 4, Kebayoran Baru',
            ],
            'andi' => [
                'name' => 'Pak Andi (Web)',
                'email' => 'customer_andi@deposusu.com',
                'phone' => '081234567899',
                'address' => 'Jl. Melawai Raya No. 45, Kebayoran Baru',
            ],
            'budi' => [
                'name' => 'Bapak Budi (Manual)',
                'email' => 'customer_budi@deposusu.com',
                'phone' => '08555444333',
                'address' => 'Komp. Polri, Pasar Minggu',
            ],
        ];

        // Seed Customers and create profiles
        $customers = [];
        foreach ($customerData as $key => $data) {
            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]);
            }
            
            $profile = MdxCustomer::where('user_id', $user->id)->first();
            if (!$profile) {
                MdxCustomer::create([
                    'user_id' => $user->id,
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                ]);
            } else {
                $profile->update([
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                ]);
            }
            $customers[$key] = $user;
        }

        // 3. Clear existing orders for clean demo state
        Schema::disableForeignKeyConstraints();
        TrxOrderItem::truncate();
        TrxOrder::truncate();
        Schema::enableForeignKeyConstraints();

        // Helper function to find a product by SKU or get fallback
        $getProduct = function ($sku) {
            $p = MdxProduct::where('sku', $sku)->first();
            if ($p) return $p;
            $p = MdxProduct::where('sku', 'like', '%' . $sku . '%')->first();
            if ($p) return $p;
            return MdxProduct::first();
        };

        // 4. Seed 6 orders ORD-001 to ORD-006
        $ordersToSeed = [
            [
                'order_number' => '#ORD-001',
                'customer_key' => 'ahmad',
                'status' => OrderStatusEnum::PREPARED,
                'payment_status' => 'PAID',
                'source' => 'app',
                'items' => [
                    ['sku' => 'SKU-001', 'qty' => 2, 'price' => 25000], // Susu Kambing Botol
                    ['sku' => 'SKU-004', 'qty' => 1, 'price' => 50000], // Yogurt Strawberry
                ]
            ],
            [
                'order_number' => '#ORD-002',
                'customer_key' => 'siti',
                'status' => OrderStatusEnum::PREPARED,
                'payment_status' => 'PAID',
                'source' => 'shopee',
                'items' => [
                    ['sku' => 'SKU-004', 'qty' => 4, 'price' => 50000], // Yogurt Strawberry
                ]
            ],
            [
                'order_number' => '#ORD-003',
                'customer_key' => 'danu',
                'status' => OrderStatusEnum::PREPARED,
                'payment_status' => 'UNPAID',
                'source' => 'tokopedia',
                'items' => [
                    ['sku' => 'SKU-003', 'qty' => 4, 'price' => 25000], // Kefir Susu Sapi
                    ['sku' => 'SKU-001', 'qty' => 1, 'price' => 25000], // Susu Kambing Botol
                ]
            ],
            [
                'order_number' => '#ORD-004',
                'customer_key' => 'dita',
                'status' => OrderStatusEnum::ON_DELIVERY,
                'payment_status' => 'UNPAID',
                'source' => 'tiktok',
                'picked_up_at' => now()->subMinutes(15),
                'items' => [
                    ['sku' => 'SKU-002', 'qty' => 3, 'price' => 100000], // Susu Sapi Murni
                ]
            ],
            [
                'order_number' => '#ORD-005',
                'customer_key' => 'andi',
                'status' => OrderStatusEnum::ON_DELIVERY,
                'payment_status' => 'UNPAID',
                'source' => 'web',
                'picked_up_at' => now()->subMinutes(10),
                'items' => [
                    ['sku' => 'SKU-001', 'qty' => 3, 'price' => 25000], // Susu Kambing Botol
                ]
            ],
            [
                'order_number' => '#ORD-006',
                'customer_key' => 'budi',
                'status' => OrderStatusEnum::DELIVERED,
                'payment_status' => 'PAID',
                'source' => 'manual',
                'picked_up_at' => now()->subMinutes(60),
                'delivered_at' => now()->subMinutes(10),
                'recipient_name' => 'Bapak Budi',
                'delivery_latitude' => -6.200000,
                'delivery_longitude' => 106.816666,
                'delivery_proof_photo' => 'https://images.unsplash.com/photo-1543083503-0c35dba51ff4?auto=format&fit=crop&w=400&h=300&q=80',
                'items' => [
                    ['sku' => 'SKU-004', 'qty' => 6, 'price' => 50000], // Yogurt Strawberry
                ]
            ],
        ];

        foreach ($ordersToSeed as $oData) {
            $customer = $customers[$oData['customer_key']];
            
            // Calculate total amount
            $totalAmount = 0;
            $itemsToCreate = [];
            
            foreach ($oData['items'] as $itemData) {
                $product = $getProduct($itemData['sku']);
                if ($product) {
                    $price = $product->price ?? $itemData['price'];
                    $subtotal = $price * $itemData['qty'];
                    $totalAmount += $subtotal;
                    
                    $itemsToCreate[] = [
                        'product_id' => $product->id,
                        'quantity' => $itemData['qty'],
                        'price' => $price,
                        'subtotal' => $subtotal,
                        'checked_quantity' => $itemData['qty'] // automatic checked quantity for driver
                    ];
                }
            }

            // Create Order
            $order = TrxOrder::create([
                'order_number' => $oData['order_number'],
                'customer_name' => $customer->name,
                'total_amount' => $totalAmount,
                'status' => $oData['status'],
                'payment_status' => $oData['payment_status'],
                'driver_id' => $driver->id,
                'warehouse_id' => $warehouse->id,
                'source' => $oData['source'],
                'picked_up_at' => $oData['picked_up_at'] ?? null,
                'delivered_at' => $oData['delivered_at'] ?? null,
                'recipient_name' => $oData['recipient_name'] ?? null,
                'recipient_signature' => isset($oData['recipient_name']) ? 'base64_signature_dummy_data' : null,
                'delivery_latitude' => $oData['delivery_latitude'] ?? null,
                'delivery_longitude' => $oData['delivery_longitude'] ?? null,
                'delivery_proof_photo' => $oData['delivery_proof_photo'] ?? null,
                'created_at' => now()->subHours(2),
                'updated_at' => now(),
            ]);

            // Create Items
            foreach ($itemsToCreate as $item) {
                $order->items()->create($item);
            }
        }

        $this->command->info('Dummy Orders #ORD-001 through #ORD-006 seeded successfully!');
    }
}
