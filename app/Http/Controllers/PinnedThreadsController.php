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
        $this->middleware('auth');
    }

    /**
     * Pin a thread.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Thread $thread)
    {
        $this->authorize('pin', $thread);

        $thread->update(['pinned' => true]);

        return Response::make('', 204);
    }

    /**
     * Unpin a thread.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('unpin', $thread);

        $thread->update(['pinned' => false]);

        return Response::make('', 204);
    }
}
