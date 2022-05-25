<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Delivery;
use Carbon\Carbon;
use Hash;

class DeliverySeeder extends Seeder
{
    private $delivery_data = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        for ($i=0; $i < 150; $i++) {
            $delivery_data[] = [
                'driver_id' => Str::random(2).'-'.random_int(10000, 99999),
                'booking_id' => Str::random(2).'-'.random_int(10000, 99999),
            ];
        }

        foreach ($delivery_data as $data) {
            Delivery::create($data);
        }
    }
}
