<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

class SaleController extends Controller
{
    public function index()
    {
        $driver_id = \Request::get('id');
        $sales = Sale::where('driver_id', $driver_id)->get();
        $balance = Sale::where('driver_id', $driver_id)->pluck('amount')->toArray();
        $balance = array_reduce($balance, function($carry, $item) {
            return $carry + str_replace(',','',$item);
        });

        return response()->json(['sales' => $sales, 'balance' => number_format($balance, 2)], 200);
    }
}
