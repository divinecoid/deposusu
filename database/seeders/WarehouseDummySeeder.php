<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MdxWarehouse;
use App\Models\MdxProduct;
use App\Models\MdxWarehouseStock;
use App\Models\MdxStockMovement;

class WarehouseDummySeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            ['name' => 'Gudang Utama Deposusu', 'address' => 'Jl. Raya Utama No. 10, Jakarta Selatan'],
            ['name' => 'Gudang Distribusi Tangerang', 'address' => 'Kawasan Industri Jatake, Tangerang'],
        ];

        foreach ($warehouses as $whData) {
            MdxWarehouse::firstOrCreate(['name' => $whData['name']], $whData);
        }

        $wh1 = MdxWarehouse::where('name', 'Gudang Utama Deposusu')->first();
        $wh2 = MdxWarehouse::where('name', 'Gudang Distribusi Tangerang')->first();
        $products = MdxProduct::all();

        $rackLetters = ['A', 'B', 'C', 'D'];

        foreach ($products as $index => $product) {
            $rack = $rackLetters[$index % count($rackLetters)] . '-' . str_pad(intval($index / count($rackLetters)) + 1, 2, '0', STR_PAD_LEFT);
            $qty1 = rand(20, 80);

            // Main warehouse stock
            MdxWarehouseStock::firstOrCreate(
                ['warehouse_id' => $wh1->id, 'product_id' => $product->id],
                ['quantity' => $qty1, 'min_stock' => 10, 'rack_location' => $rack]
            );

            // Some products also in second warehouse
            if ($index % 2 == 0 && $wh2) {
                $qty2 = rand(10, 40);
                MdxWarehouseStock::firstOrCreate(
                    ['warehouse_id' => $wh2->id, 'product_id' => $product->id],
                    ['quantity' => $qty2, 'min_stock' => 5, 'rack_location' => 'R-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT)]
                );
            }
        }
    }
}
