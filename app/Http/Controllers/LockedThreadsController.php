<?php

namespace App\Http\Controllers;

use App\Models\Thread;
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
        $this->middleware('members-area');
    }

    /**
     * Lock a thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Thread $thread)
    {
        $this->authorize('lock', $thread);

        $thread->update(['locked' => true]);

        return Response::make('', 204);
    }

    /**
     * Unlock a thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('unlock', $thread);

        $thread->update(['locked' => false]);

        return Response::make('', 204);
    }
}
