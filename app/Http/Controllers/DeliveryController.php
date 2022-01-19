<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveriesCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function index()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $deliveries = new DeliveriesCollection(Delivery::paginate($page_number));

        $to_deliver = Delivery::where('status', 3)->orderBy('delivery_date', 'ASC')->get();
        $in_transit = Delivery::where('status', 2)->orderBy('delivery_date', 'ASC')->get();
        $delivered = Delivery::where('status', 1)->orderBy('delivery_date', 'ASC')->get();

        return response()->json(['deliveries' => $deliveries, 'to_deliver' => $to_deliver, 'in_transit' => $in_transit, 'delivered' => $delivered], 200);
    }

    public function search()
    {
        $key = \Request::get('q');
        $entries = \Request::get('entries');
        $page_number = $entries;

        $delivery = Delivery::where('delivery_id','LIKE',"%{$key}%")
        ->orWhere('delivery_date','LIKE',"%{$key}%")
        ->orWhere('origin','LIKE',"%{$key}%")
        ->orWhere('destination','LIKE',"%{$key}%")
        ->orWhere('cost','LIKE',"%{$key}%")
        ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'In Transit' ELSE 'To Deliver' END) LIKE '%{$key}%'")
        ->paginate($page_number);

        $deliveries = new DeliveriesCollection($delivery);

        return response()->json(['deliveries' => $deliveries], 200);
    }
}