<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::create([
            'method_name' => 'JazzCash',
        ]);
        PaymentMethod::create([
            'method_name' => 'Meezan Bank',
        ]);
        PaymentMethod::create([
            'method_name' => 'HBL',
        ]);
        PaymentMethod::create([
            'method_name' => 'Bank Al Habib',
        ]);
    }
}
