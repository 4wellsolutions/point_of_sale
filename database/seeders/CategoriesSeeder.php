<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Beverages', 'description' => 'Drinks and liquids'],
            ['name' => 'Snacks', 'description' => 'Quick snack items'],
            ['name' => 'Tobacco', 'description' => 'Shisha tobacco'],
            // Add more categories as needed
        ];

        DB::table('categories')->insert($categories);
    }
}
