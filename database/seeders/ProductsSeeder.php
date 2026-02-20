<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Shisha Tobacco 500g', 'description' => 'Premium quality tobacco', 'sku' => 'SHI-001', 'flavour_id' => 1, 'packing_id' => 1, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890123', 'weight' => 500, 'volume' => null, 'status' => 'active'],
            ['name' => 'Fruit Punch Shisha', 'description' => 'Fruity blend of flavors', 'sku' => 'SHI-002', 'flavour_id' => 2, 'packing_id' => 2, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890124', 'weight' => 200, 'volume' => null, 'status' => 'active'],
            ['name' => 'Mint Shisha 200g', 'description' => 'Refreshing mint flavor', 'sku' => 'SHI-003', 'flavour_id' => 1, 'packing_id' => 3, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890125', 'weight' => 200, 'volume' => null, 'status' => 'active'],
            ['name' => 'Peach Shisha 500g', 'description' => 'Delicious peach flavor', 'sku' => 'SHI-004', 'flavour_id' => 3, 'packing_id' => 1, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890126', 'weight' => 500, 'volume' => null, 'status' => 'active'],
            ['name' => 'Grape Shisha 200g', 'description' => 'Sweet grape flavor', 'sku' => 'SHI-005', 'flavour_id' => 4, 'packing_id' => 2, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890127', 'weight' => 200, 'volume' => null, 'status' => 'active'],
            ['name' => 'Lemon Shisha 500g', 'description' => 'Tangy lemon flavor', 'sku' => 'SHI-006', 'flavour_id' => 5, 'packing_id' => 1, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890128', 'weight' => 500, 'volume' => null, 'status' => 'active'],
            ['name' => 'Mixed Fruit Shisha', 'description' => 'Blend of tropical fruits', 'sku' => 'SHI-007', 'flavour_id' => 2, 'packing_id' => 3, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890129', 'weight' => 300, 'volume' => null, 'status' => 'active'],
            ['name' => 'Berry Blast Shisha', 'description' => 'Blend of berries', 'sku' => 'SHI-008', 'flavour_id' => 2, 'packing_id' => 1, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890130', 'weight' => 400, 'volume' => null, 'status' => 'active'],
            ['name' => 'Tropical Punch Shisha', 'description' => 'Tropical fruit mix', 'sku' => 'SHI-009', 'flavour_id' => 3, 'packing_id' => 2, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890131', 'weight' => 250, 'volume' => null, 'status' => 'active'],
            ['name' => 'Watermelon Shisha 500g', 'description' => 'Refreshing watermelon flavor', 'sku' => 'SHI-010', 'flavour_id' => 4, 'packing_id' => 1, 'category_id' => 3, 'image' => 'products/product.png', 'barcode' => '1234567890132', 'weight' => 500, 'volume' => null, 'status' => 'active'],
        ];

        DB::table('products')->insert($products);
    }
}
