<?php

namespace App\Http\Controllers;

use App\Poll;
use App\PollVote;
use App\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $channelSlug, Thread $thread)
    {
        $poll = $thread->poll;
        $this->authorize('viewAny', [PollVote::class, $poll]);

        return $poll->votes()->groupBy(['option_id'])->get(['option_id', DB::raw('COUNT(*) AS votes_number')]);
    }

    /**
     * Display the search results.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(string $channelSlug, Thread $thread)
    {
        $poll = $thread->poll;

        return $poll->votes()->where('user_id', '=', Auth::id())->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(string $channelSlug, Thread $thread)
    {
        $poll = $thread->poll;
        $this->authorize('create', $poll);

        return view('polls.vote', ['channelSlug' => json_decode($channelSlug)->name, 'thread' => $thread, 'poll' => $poll]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function store(Request $request, string $channelSlug, Thread $thread)
    {
        throw_if(
            is_null($poll = $thread->poll),
            (new ModelNotFoundException)->setModel(Poll::class)
        );

        $this->authorize('vote', $poll);

        $request->validate([
            'vote' => ['array', "max:{$poll->max_votes}"],
        ]);

        $poll->castVote($request->input('vote'));

        return Response::make($poll->votes, HttpResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PollVote  $pollVote
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, PollVote $pollVote)
    {
        $this->authorize('delete', $pollVote);
        $pollVote->delete();

        if ($request->expectsJson()) {
            return Response::make(['status' => 'Poll vote deleted.']);
        }

        return back()
            ->with('flash', 'The poll vote was deleted.');
    }
}
