<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsController extends Controller
{

    /**
     * constructor
     */

    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * retrieve notifications from the database
     *
     * @return void
     */
    public function getNotifications()
    {
        $user = request()->user();

        $user->unreadNotifications->markAsRead();

        return response()->json($user->notifications);
    }
}
