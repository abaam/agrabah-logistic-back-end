<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveriesCollection;
use Illuminate\Http\Request;
use App\Models\Delivery;
use Illuminate\Http\Response;

class DeliveryController extends Controller
{
    public function index()
    {
        $entries = \Request::get('entries');

        $page_number = $entries;

        return response()->json(
            new DeliveriesCollection(Delivery::paginate($page_number)),
            Response::HTTP_OK
        );
    }

    public function search()
    {
        $key = \Request::get('q');
        $delivery = Delivery::where('delivery_id','LIKE',"%{$key}%")->orWhere('delivery_date','LIKE',"%{$key}%")->orWhere('origin','LIKE',"%{$key}%")
                ->orWhere('destination','LIKE',"%{$key}%")->orWhere('cost','LIKE',"%{$key}%")->get();

        return response()->json(['delivery' => $delivery], 200);
    }
}
