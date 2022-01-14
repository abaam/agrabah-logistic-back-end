<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Delivery;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deliveries =  [
            [
                'delivery_date' => 'Jan 06, 2022',
                'delivery_id' => 'AL-00003',
                'origin' => 'Bitano, Legazpi City',
                'destination' => 'Nabua, Camarines Sur',
                'cost' => 'P 3,500.00',
                'status' => '3',
            ],
            [
                'delivery_date' => 'Jan 04, 2022',
                'delivery_id' => 'AL-00002',
                'origin' => 'Bitano, Legazpi City',
                'destination' => 'San Rapael, Sto. Domingo',
                'cost' => 'P 5,500.00',
                'status' => '2',
            ],
            [
                'delivery_date' => 'Jan 02, 2022',
                'delivery_id' => 'AL-00001',
                'origin' => 'Placer, Masbate City',
                'destination' => 'Virac, Catanduanes',
                'cost' => 'P 2,500.00',
                'status' => '1',
            ]
        ];

        Delivery::insert($deliveries);
    }
}
