<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomersSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            ['name' => 'John Doe', 'email' => 'customer1@example.com', 'phone' => '1234567890', 'address' => 'Customer Address 1', 'whatsapp' => '1234567890', 'type_id' => 1, 'image' => null],
            ['name' => 'Jane Smith', 'email' => 'customer2@example.com', 'phone' => '0987654321', 'address' => 'Customer Address 2', 'whatsapp' => '0987654321', 'type_id' => 2, 'image' => null],
            ['name' => 'Alice Johnson', 'email' => 'customer3@example.com', 'phone' => '2345678901', 'address' => 'Customer Address 3', 'whatsapp' => '2345678901', 'type_id' => 1, 'image' => null],
            ['name' => 'Bob Brown', 'email' => 'customer4@example.com', 'phone' => '3456789012', 'address' => 'Customer Address 4', 'whatsapp' => '3456789012', 'type_id' => 2, 'image' => null],
            ['name' => 'Charlie Davis', 'email' => 'customer5@example.com', 'phone' => '4567890123', 'address' => 'Customer Address 5', 'whatsapp' => '4567890123', 'type_id' => 1, 'image' => null],
            ['name' => 'David Wilson', 'email' => 'customer6@example.com', 'phone' => '5678901234', 'address' => 'Customer Address 6', 'whatsapp' => '5678901234', 'type_id' => 2, 'image' => null],
            ['name' => 'Eva Martinez', 'email' => 'customer7@example.com', 'phone' => '6789012345', 'address' => 'Customer Address 7', 'whatsapp' => '6789012345', 'type_id' => 1, 'image' => null],
            ['name' => 'Frank Lopez', 'email' => 'customer8@example.com', 'phone' => '7890123456', 'address' => 'Customer Address 8', 'whatsapp' => '7890123456', 'type_id' => 2, 'image' => null],
            ['name' => 'Grace Taylor', 'email' => 'customer9@example.com', 'phone' => '8901234567', 'address' => 'Customer Address 9', 'whatsapp' => '8901234567', 'type_id' => 1, 'image' => null],
            ['name' => 'Henry Moore', 'email' => 'customer10@example.com', 'phone' => '9012345678', 'address' => 'Customer Address 10', 'whatsapp' => '9012345678', 'type_id' => 2, 'image' => null],
        ];

        DB::table('customers')->insert($customers);
    }
}
