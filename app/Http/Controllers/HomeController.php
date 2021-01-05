<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Channel;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\DB;

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
            'threadUpdates' => Activity
                ::with('subject')
                ->whereIn('type', ['created_thread', 'created_post'])
                ->latest()
                ->take(16)
                ->get()
                ->map(function ($activity) {
                    if ($activity->type === 'created_thread') {
                        return $activity->subject;
                    }

                    return $activity->subject->thread;
                })
                ->whereIn('channel_id', Channel::withPermission('view')->pluck('id')->all())
                ->unique('id')
                ->take(8),
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
