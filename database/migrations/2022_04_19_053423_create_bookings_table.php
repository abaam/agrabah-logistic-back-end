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
            $table->string('package_item');
            $table->string('package_quantity');
            $table->string('package_unit');
            $table->longText('package_note');
            $table->string('receiver_name');
            $table->string('receiver_contact');
            $table->string('vehicle_type');
            $table->string('pick_up');
            $table->string('drop_off');
            $table->string('date_time');
            $table->string('payment_method');
            $table->boolean('payment_status')->default(0)->comment('0 = Pending, 1 = Paid');
            $table->boolean('status')->default(3)->comment('1 = Delivered, 2 = To Receive, 3 = To Ship');
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
