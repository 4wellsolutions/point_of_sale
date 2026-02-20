<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypesSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Retailer'],
            ['name' => 'Wholesaler'],
            ['name' => 'Distributor'],
            // Add more types as needed
        ];

        DB::table('types')->insert($types);
    }
}
