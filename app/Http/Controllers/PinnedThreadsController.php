<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Support\Facades\Response;

class PinnedThreadsController extends Controller
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
     * Pin a thread.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread)
    {
        $thread->update(['pinned' => true]);

        return Response::make('', 204);
    }

    /**
     * Unpin a thread.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $thread->update(['pinned' => false]);

        return Response::make('', 204);
    }
}
