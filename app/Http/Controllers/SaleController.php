<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\SalesCollection;
use App\Models\Sale;

class SaleController extends Controller
{
    public function index()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $sales = new SalesCollection(Sale::paginate($page_number));

        $paymaya = Sale::where('payment_method', 0)->orderBy('created_at', 'ASC')->get();
        $gcash = Sale::where('payment_method', 1)->orderBy('created_at', 'ASC')->get();
        $cash_on_delivery = Sale::where('payment_method', 2)->orderBy('created_at', 'ASC')->get();

        return response()->json(['sales' => $sales, 'paymaya' => $paymaya, 'gcash' => $gcash, 'cash_on_delivery' => $cash_on_delivery], 200);
    }

    public function wallet()
    {
        $driver_id = \Request::get('id');
        $sales = Sale::where('driver_id', $driver_id)->get();
        $balance = Sale::where('driver_id', $driver_id)->pluck('amount')->toArray();
        $balance = array_reduce($balance, function($carry, $item) {
            return $carry + str_replace(',','',$item);
        });

        return response()->json(['sales' => $sales, 'balance' => number_format($balance, 2)], 200);
    }

    public function search()
    {
        $key = \Request::get('q');
        $entries = \Request::get('entries');
        $page_number = $entries;

        $Sale = Sale::where('booking_id','LIKE',"%{$key}%")
        ->orWhere('full_name','LIKE',"%{$key}%")
        ->orWhere('mobile_number','LIKE',"%{$key}%")
        ->orWhere('amount','LIKE',"%{$key}%")
        ->orWhere('ref_number','LIKE',"%{$key}%")
        ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' WHEN payment_method = 2 THEN 'Cash On Delivery' END) LIKE '%{$key}%'")
        ->paginate($page_number);

        $sales = new SalesCollection($Sale);

        return response()->json(['sales' => $sales], 200);
    }
}
