<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_item',
        'package_quantity',
        'package_unit',
        'package_note',
        'receiver_name',
        'receiver_contact',
        'vehicle_type',
        'pick_up',
        'drop_off',
        'date_time',
        'payment_method',
        'status',
    ];
}
