<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlavoursSeeder extends Seeder
{
    public function run()
    {
        $flavours = [
            ['name' => 'Mint', 'description' => 'Refreshing mint flavor'],
            ['name' => 'Fruit Punch', 'description' => 'Sweet fruit punch blend'],
            ['name' => 'Lemon', 'description' => 'Tart and tangy lemon'],
            ['name' => 'Peach', 'description' => 'Juicy and ripe peach'],
            ['name' => 'Grape', 'description' => 'Sweet grape flavor'],
            // Add more flavours as needed
        ];

        DB::table('flavours')->insert($flavours);
    }
}
