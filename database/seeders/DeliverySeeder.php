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
                'delivery_date' => Carbon::today()->subDays(rand(0, 365)),
                'delivery_id' => Str::random(2).'-'.random_int(10000, 99999),
                'origin' => Str::random(15),
                'destination' => Str::random(15),
                'cost' => 'P '.rand(1,9).','.random_int(000, 999),
                'status' => rand(1,3)
            ];
        }

        foreach ($delivery_data as $data) {
            Delivery::create($data);
        }
    }
}
