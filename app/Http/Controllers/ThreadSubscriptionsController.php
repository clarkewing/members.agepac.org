<?php

namespace App\Http\Controllers;

use App\Models\Thread;

class ThreadSubscriptionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  string  $channelSlug
     * @param  \App\Models\Thread  $thread
     * @return void
     */
    public function store(string $channelSlug, Thread $thread): void
    {
        $thread->subscribe();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $channelSlug
     * @param  \App\Models\Thread  $thread
     * @return void
     */
    public function destroy(string $channelSlug, Thread $thread): void
    {
        $thread->unsubscribe();
    }
}
