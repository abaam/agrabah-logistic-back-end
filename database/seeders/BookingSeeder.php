<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 150; $i++) {
            $booking_data[] = [
                'package_item' => Str::random(6),
                'package_quantity' => Str::random(2).'-'.random_int(000, 100),
                'package_unit' => Str::random(2),
                'package_note' => Str::random(20),
                'receiver_name' => Str::random(6),
                'receiver_contact' => random_int(00000000000, 99999999999),
                'vehicle_type' => Str::random(6),
                'pick_up' => Str::random(6),
                'drop_off' => Str::random(6),
                'date_time' => Str::random(6),
                'payment_method' => random_int(0, 1),
                'payment_status' => random_int(0, 1),
                'package_item' => Str::random(6),
                'status' => random_int(1, 3),
            ];
        }

        foreach ($booking_data as $data) {
            Booking::create($data);
        }
    }
}
