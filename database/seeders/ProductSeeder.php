<?php

namespace Database\Seeders;

use App\Models\MdxProduct;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Greenfields Yogurt Drink Mango',
                'price' => 35000,
                'description' => 'Yogurt drink dengan rasa mangga segar yang nikmat',
                'image' => 'https://assets.klikindomaret.com/products/20063231/20063231_1.jpg',
                'stock' => 100,
            ],
            [
                'name' => 'Greenfields Yogurt Drink Blueberry',
                'price' => 35000,
                'description' => 'Yogurt drink dengan rasa blueberry yang menyegarkan',
                'image' => 'https://assets.klikindomaret.com/products/20063232/20063232_1.jpg',
                'stock' => 100,
            ],
            [
                'name' => 'Greenfields Yogurt Drink Strawberry',
                'price' => 35000,
                'description' => 'Yogurt drink dengan rasa strawberry yang manis',
                'image' => 'https://assets.klikindomaret.com/products/20063233/20063233_1.jpg',
                'stock' => 100,
            ],
            [
                'name' => 'Greenfields Yogurt Drink Lychee',
                'price' => 35000,
                'description' => 'Yogurt drink dengan rasa lychee yang eksotis',
                'image' => 'https://assets.klikindomaret.com/products/20063234/20063234_1.jpg',
                'stock' => 100,
            ],
            [
                'name' => 'Greenfields Yogurt Drink Mixed Berry',
                'price' => 45000,
                'description' => 'Yogurt drink dengan campuran berry yang kaya rasa',
                'image' => 'https://assets.klikindomaret.com/products/20063235/20063235_1.jpg',
                'stock' => 100,
            ],
            [
                'name' => 'Greenfields Yogurt Drink Peach',
                'price' => 35000,
                'description' => 'Yogurt drink dengan rasa peach yang lembut',
                'image' => 'https://assets.klikindomaret.com/products/20063236/20063236_1.jpg',
                'stock' => 100,
            ],
        ];

        foreach ($products as $product) {
            MdxProduct::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
