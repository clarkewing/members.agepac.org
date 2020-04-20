<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class ThreadSubscriptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $channel
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, string $channelSlug, Thread $thread)
    {
        $thread->subscribe();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $channel
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $channelSlug, Thread $thread)
    {
        $thread->unsubscribe();
    }
}
