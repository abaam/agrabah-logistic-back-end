<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        	'phone_number' => '09669510498',
            'password' => bcrypt('driver@123'),
            'register_as' => 1,
            'pin' => '000000',
            'verified' => 1
        ]);

        User::create([
        	'phone_number' => '09108108998',
            'password' => bcrypt('customer@123'),
            'register_as' => 2,
            'pin' => '101010',
            'verified' => 1
        ]);

        User::create([
            'phone_number' => '09108108999',
            'password' => bcrypt('admin@123'),
            'register_as' => 3,
            'pin' => '111111',
            'verified' => 1
        ]);
    }
}
