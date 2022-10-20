<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_updates', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id');
            $table->string('booking_id');
            $table->string('driver_name');
            $table->string('receiver_name');
            $table->string('tracking_status');
            $table->string('location');
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
        Schema::dropIfExists('tracking_updates');
    }
}
