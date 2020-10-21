<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePollRequest;
use App\Poll;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PollsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index');
        $this->middleware('verified:polls,Tu dois vérifier ton adresse email avant de pouvoir cérer un sondage.')
            ->only(['store']);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function show(Poll $poll)
    {
        return $poll;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $channelSlug, Thread $thread)
    {
        return $thread->poll()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(string $channelSlug, Thread $thread)
    {
        return view('polls.create', ['channelSlug' => json_decode($channelSlug)->name, 'thread' => $thread]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreatePollRequest  $request
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePollRequest $request, string $channelSlug, Thread $thread)
    {
        $poll = $thread->addPoll([
            'title' => $request->input('title'),
            'votes_editable' => $request->input('votes_editable'),
            'max_votes' => $request->input('max_votes'),
            'votes_privacy' => $request->input('votes_privacy'),
            'results_before_voting' => $request->input('results_before_voting'),
            'locked_at' => $request->input('locked_at'),
        ]);

        $poll->addOptions($request->input('options'));

        return Response::make($poll->fresh(), HttpResponse::HTTP_CREATED);
    }

    /**
     * Display the poll results.
     *
     * @return \Illuminate\Http\Response
     */
    public function results(string $channelSlug, Thread $thread)
    {
        $poll = $thread->poll;
        $this->authorize('viewResults', $poll);

        return view('polls.results', ['channelSlug' => json_decode($channelSlug)->name, 'thread' => $thread, 'poll' => $poll]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poll $poll)
    {
        $this->authorize('update', $poll);

        $request->validate([
            'votes_editable' => 'boolean',
            'max_votes' => 'nullable|digits_between:1,1000000',
            'votes_privacy' => 'digits_between:0,2',
            'results_before_voting' => 'boolean', ]);

        $poll->update($request->only(['title', 'votes_editable', 'max_votes', 'votes_privacy', 'results_before_voting', 'locked_at']));
    }

    /**
     * Lock the poll.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function lock(Request $request, Poll $poll)
    {
        $this->authorize('lock', $poll);

        $poll->update(['locked_at' => Carbon::now()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, Poll $poll)
    {
        $this->authorize('delete', $poll);

        $poll->delete();

        if ($request->expectsJson()) {
            return Response::make(['status' => 'Poll deleted.']);
        }

        return back()
            ->with('flash', 'The poll was deleted.');
    }
}
