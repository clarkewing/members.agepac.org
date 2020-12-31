<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Thread;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('members-area');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home', [
            'threadUpdates' => Thread
                ::orderBy('updated_at', 'desc')
                ->take(8)
                ->get(),
            'latestAnnouncement' => Thread
                ::whereIn('channel_id', [1, 5, 43, 54]) // Association and children
                ->where('pinned', true)
                ->latest()
                ->first(),
            'feed' => Activity
                ::with('subject')
                ->whereIn('type', ['created_user', 'updated_profile', 'created_thread', 'created_post'])
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}
