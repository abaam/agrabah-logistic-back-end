<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::paginate(10);
        
        return response()->json($deliveries);
    }

    public function show()
    {
        $entries = \Request::get('entries');

        if ($entries) {
            $deliveries = Delivery::paginate($entries);
        } else {
            $deliveries = Delivery::paginate(10);
        }

        return response()->json($deliveries);
    }

    public function search()
    {
        $key = \Request::get('q');
        $delivery = Delivery::where('delivery_id','LIKE',"%{$key}%")->orWhere('delivery_date','LIKE',"%{$key}%")->orWhere('origin','LIKE',"%{$key}%")
                ->orWhere('destination','LIKE',"%{$key}%")->orWhere('cost','LIKE',"%{$key}%")->get();

        return response()->json(['delivery' => $delivery], 200);
    }
}
