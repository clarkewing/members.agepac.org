<?php

namespace App\Http\Controllers;

use App\Poll;
use App\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PollVotesController extends Controller
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function update(Request $request, string $channelSlug, Thread $thread)
    {
        throw_if(
            is_null($poll = $thread->poll),
            (new ModelNotFoundException)->setModel(Poll::class)
        );

        $this->authorize('vote', $poll);

        $request->validate(['vote' => 'array']);

        if (! is_null($poll->max_votes)) {
            $request->validate(['vote' => "max:{$poll->max_votes}"]);
        }

        $poll->castVote($request->input('vote'));

        return Response::make($poll->getVote());
    }
}
