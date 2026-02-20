<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackingsSeeder extends Seeder
{
    public function run()
    {
        $packings = [
            ['type' => 'Bottle', 'unit_size' => '500 ml', 'description' => 'Glass bottle with 500 ml capacity'],
            ['type' => 'Box', 'unit_size' => '200 gm', 'description' => 'Cardboard box with 200 gm capacity'],
            ['type' => 'Can', 'unit_size' => '330 ml', 'description' => 'Aluminum can with 330 ml capacity'],
            // Add more packings as needed
        ];

        DB::table('packings')->insert($packings);
    }
}
