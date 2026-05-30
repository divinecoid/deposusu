<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MdxProduct;
use App\Models\MdxCategory;
use Illuminate\Support\Str;

class ProductDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada kategori Susu UHT
        $category = MdxCategory::firstOrCreate([
            'slug' => 'susu-uht'
        ], [
            'name' => 'Susu UHT'
        ]);

        $products = [
            [
                'name' => 'Susu Ultra Milk 1000ml',
                'price' => 18000,
                'stock' => 100,
                'variants' => [
                    ['name' => 'Full Cream', 'price' => 18000, 'stock' => 50],
                    ['name' => 'Coklat', 'price' => 18500, 'stock' => 50],
                ],
                'wholesales' => [
                    ['min_qty' => 12, 'price' => 17500], // 1 dus
                    ['min_qty' => 50, 'price' => 17000],
                ]
            ],
            [
                'name' => 'Susu Bear Brand 189ml',
                'price' => 10500,
                'stock' => 200,
                'variants' => [],
                'wholesales' => [
                    ['min_qty' => 30, 'price' => 9800], // 1 dus
                ]
            ],
            [
                'name' => 'Susu Greenfields UHT 1000ml',
                'price' => 22000,
                'stock' => 80,
                'variants' => [
                    ['name' => 'Fresh Milk', 'price' => 22000, 'stock' => 40],
                    ['name' => 'Choco Malt', 'price' => 22500, 'stock' => 40],
                ],
                'wholesales' => [
                    ['min_qty' => 12, 'price' => 21000],
                ]
            ],
            [
                'name' => 'Susu Frisian Flag UHT 900ml',
                'price' => 16500,
                'stock' => 150,
                'variants' => [
                    ['name' => 'Full Cream', 'price' => 16500, 'stock' => 75],
                    ['name' => 'Swiss Chocolate', 'price' => 16500, 'stock' => 75],
                ],
                'wholesales' => [
                    ['min_qty' => 12, 'price' => 15800],
                ]
            ],
            [
                'name' => 'Indomilk UHT 1000ml',
                'price' => 15500,
                'stock' => 120,
                'variants' => [
                    ['name' => 'Plain', 'price' => 15500, 'stock' => 60],
                    ['name' => 'Coklat', 'price' => 15500, 'stock' => 60],
                ],
                'wholesales' => [
                    ['min_qty' => 12, 'price' => 14900],
                ]
            ],
            [
                'name' => 'Ovaltine UHT 1000ml',
                'price' => 24000,
                'stock' => 60,
                'variants' => [],
                'wholesales' => [
                    ['min_qty' => 12, 'price' => 23000],
                ]
            ],
            [
                'name' => 'Milo UHT 1000ml',
                'price' => 23500,
                'stock' => 90,
                'variants' => [],
                'wholesales' => [
                    ['min_qty' => 12, 'price' => 22500],
                ]
            ],
            [
                'name' => 'Cimory UHT 250ml',
                'price' => 6500,
                'stock' => 300,
                'variants' => [
                    ['name' => 'Strawberry', 'price' => 6500, 'stock' => 100],
                    ['name' => 'Blueberry', 'price' => 6500, 'stock' => 100],
                    ['name' => 'Matcha', 'price' => 6500, 'stock' => 100],
                ],
                'wholesales' => [
                    ['min_qty' => 24, 'price' => 6000],
                ]
            ],
            [
                'name' => 'Diamond UHT 1000ml',
                'price' => 17000,
                'stock' => 110,
                'variants' => [
                    ['name' => 'Full Cream', 'price' => 17000, 'stock' => 55],
                    ['name' => 'Coklat', 'price' => 17000, 'stock' => 55],
                ],
                'wholesales' => [
                    ['min_qty' => 12, 'price' => 16200],
                ]
            ],
            [
                'name' => 'Kin Fresh Milk 1000ml (A2 Cow)',
                'price' => 28000,
                'stock' => 40,
                'variants' => [],
                'wholesales' => [
                    ['min_qty' => 12, 'price' => 27000],
                ]
            ],
        ];

        foreach ($products as $prodData) {
            $sku = 'DP-' . strtoupper(Str::random(6));
            
            $product = MdxProduct::create([
                'name' => $prodData['name'],
                'sku' => $sku,
                'barcode' => $sku,
                'price' => $prodData['price'],
                'stock' => $prodData['stock'],
                'low_stock_threshold' => 10,
            ]);

            $product->categories()->attach($category->id);

            foreach ($prodData['variants'] as $variantData) {
                $product->variants()->create([
                    'sku' => $sku . '-' . strtoupper(Str::random(3)),
                    'name' => $variantData['name'],
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);
            }

            foreach ($prodData['wholesales'] as $wholesaleData) {
                $product->wholesales()->create([
                    'min_qty' => $wholesaleData['min_qty'],
                    'price' => $wholesaleData['price'],
                ]);
            }
        }
    }
}
