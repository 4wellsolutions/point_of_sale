<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ahmad (Admin)',
                'email' => '4wellsolutions@gmail.com',
                'password' => Hash::make('asdfasdf'),
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@pos.test',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Cashier',
                'email' => 'cashier@pos.test',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
