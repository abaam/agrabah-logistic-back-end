<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {

        $user_id = \Request::get('user_id');
        $role = \Request::get('role');

        if ($role == 1) {
            $notifications = Notification::where('to_user_id', 'Driver')->orWhere('description','LIKE',"%payment has been approved%")->orderBy('created_at', 'DESC')->get();
            $notifications_count = Notification::where(function ($query) {
                $query->where('to_user_id', 'Driver')->orWhere('description','LIKE',"%payment has been approved%");
                })->where('seen', 0)->count();
        } else {
            $notifications = Notification::where('to_user_id', $user_id)->orderBy('created_at', 'DESC')->get();
            $notifications_count = Notification::where('to_user_id', $user_id)->where('seen', 0)->count();
        }

        return response()->json(['notifications' => $notifications, 'count' => $notifications_count], 200);
    }

    public function view()
    {

        $id = \Request::get('id');

        Notification::where('id', $id)->update([
            'seen' => 1
        ]);

        return response()->json('Success!');
    }
}
