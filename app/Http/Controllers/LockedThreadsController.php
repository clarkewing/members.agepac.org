<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Support\Facades\Response;

class LockedThreadsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Lock a thread.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread)
    {
        $thread->update(['locked' => true]);

        return Response::make('', 204);
    }

    /**
     * Unlock a thread.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $thread->update(['locked' => false]);

        return Response::make('', 204);
    }
}
