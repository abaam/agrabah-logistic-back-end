<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id')->unique();
            $table->string('tracking_id')->unique()->nullable();
            $table->string('driver_name')->nullable();
            $table->string('package_item');
            $table->string('package_quantity');
            $table->string('package_unit');
            $table->longText('package_note');
            $table->string('receiver_name');
            $table->string('receiver_contact');
            $table->string('vehicle_type');
            $table->string('pick_up');
            $table->string('pick_up_complete_address');
            $table->string('drop_off');
            $table->string('drop_off_complete_address');
            $table->string('date_time');
            $table->string('payment_total');
            $table->string('payment_method')->default(0)->comment('0 = Paymaya, 1 = Gcash, 2 = Cash On Delivery');
            $table->boolean('payment_status')->default(0)->comment('0 = Pending, 1 = Pending Approval, 2 = Paid, 3 = Cancelled');
            $table->boolean('status')->default(3)->comment('1 = Delivered, 2 = To Receive, 3 = For Pickup, 4 = Cancelled, 5 = Accepted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
