<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserNotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of unread notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Auth::user()->unreadNotifications;
    }

    /**
     * Mark the specified notification as read.
     *
     * @param  string $notificationId
     * @return \Illuminate\Http\Response
     */
    public function destroy($notificationId)
    {
        Auth::user()->notifications()->findOrFail($notificationId)->markAsRead();
    }
}
