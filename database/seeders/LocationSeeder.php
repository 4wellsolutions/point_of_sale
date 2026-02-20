<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert shelves "Shelf 1" to "Shelf 10"
        for ($i = 1; $i <= 10; $i++) {
            Location::create([
                'name' => 'Shelf ' . $i,
            ]);
        }
    }
}
