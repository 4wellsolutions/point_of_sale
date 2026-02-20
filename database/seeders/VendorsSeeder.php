<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorsSeeder extends Seeder
{
    public function run()
    {
        $vendors = [
            ['name' => 'ABC Corp', 'email' => 'vendor1@example.com', 'phone' => '1234567890', 'address' => 'Street 1, City', 'whatsapp' => '1234567890', 'type_id' => 1, 'image' => null],
            ['name' => 'XYZ Ltd', 'email' => 'vendor2@example.com', 'phone' => '0987654321', 'address' => 'Street 2, City', 'whatsapp' => '0987654321', 'type_id' => 2, 'image' => null],
            ['name' => 'Global Traders', 'email' => 'vendor3@example.com', 'phone' => '2345678901', 'address' => 'Street 3, City', 'whatsapp' => '2345678901', 'type_id' => 1, 'image' => null],
            ['name' => 'Premium Supplies', 'email' => 'vendor4@example.com', 'phone' => '3456789012', 'address' => 'Street 4, City', 'whatsapp' => '3456789012', 'type_id' => 2, 'image' => null],
            ['name' => 'Shisha World', 'email' => 'vendor5@example.com', 'phone' => '4567890123', 'address' => 'Street 5, City', 'whatsapp' => '4567890123', 'type_id' => 1, 'image' => null],
            ['name' => 'Tobacco King', 'email' => 'vendor6@example.com', 'phone' => '5678901234', 'address' => 'Street 6, City', 'whatsapp' => '5678901234', 'type_id' => 2, 'image' => null],
            ['name' => 'Shisha Masters', 'email' => 'vendor7@example.com', 'phone' => '6789012345', 'address' => 'Street 7, City', 'whatsapp' => '6789012345', 'type_id' => 1, 'image' => null],
            ['name' => 'Smoke House', 'email' => 'vendor8@example.com', 'phone' => '7890123456', 'address' => 'Street 8, City', 'whatsapp' => '7890123456', 'type_id' => 2, 'image' => null],
            ['name' => 'Vape World', 'email' => 'vendor9@example.com', 'phone' => '8901234567', 'address' => 'Street 9, City', 'whatsapp' => '8901234567', 'type_id' => 1, 'image' => null],
            ['name' => 'Tobacco Empire', 'email' => 'vendor10@example.com', 'phone' => '9012345678', 'address' => 'Street 10, City', 'whatsapp' => '9012345678', 'type_id' => 2, 'image' => null],
        ];

        DB::table('vendors')->insert($vendors);
    }
}
