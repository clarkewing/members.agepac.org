<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePollRequest;
use App\Http\Requests\UpdatePollRequest;
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
     * Show the poll.
     *
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $channelSlug, Thread $thread)
    {
        $poll = $thread->poll;

        $poll->vote = $poll->getVote();

        return Response::json($poll);
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
     * @param  \App\Http\Requests\StorePollRequest  $request
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function store(StorePollRequest $request, string $channelSlug, Thread $thread)
    {
        $poll = $thread->addPoll($request->all());

        $poll->syncOptions($request->input('options'));

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
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePollRequest  $request
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePollRequest $request, string $channelSlug, Thread $thread)
    {
        $poll = $thread->poll;

        $poll->fill($request->all())->save();

        $poll->syncOptions($request->input('options'));

        return Response::json($poll);
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
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, string $channelSlug, Thread $thread)
    {
        $poll = $thread->poll;

        $this->authorize('delete', $poll);

        $poll->delete();

        if ($request->expectsJson()) {
            return Response::noContent();
        }

        return back()
            ->with('flash', 'The poll was deleted.');
    }
}
