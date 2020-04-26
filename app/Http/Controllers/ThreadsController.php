<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadFilters;
use App\Thread;
use App\Trending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ThreadsController extends Controller
{
    /**
     * Create a new ThreadController instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('verified:threads,Tu dois vérifier ton adresse email avant de pouvoir publier.')
            ->only(['create', 'store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Channel $channel
     * @param  \App\Filters\ThreadFilters $filters
     * @param  \App\Trending $trending
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filters, Trending $trending, Request $request)
    {
        $threads = $this->getThreads($channel, $filters);

        if ($request->expectsJson()) {
            return $threads;
        }

        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'channel_id' => 'required|exists:channels,id',
        ]);

        $thread = Thread::create([
            'user_id' => Auth::id(),
            'channel_id' => $request->input('channel_id'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
        ]);

        if ($request->wantsJson()) {
            return Response::make($thread, 201);
        }

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Channel  $channel
     * @param  \App\Thread  $thread
     * @param  \App\Trending $trending
     * @return \Illuminate\Http\Response
     */
    public function show(Channel $channel, Thread $thread, Trending $trending)
    {
        if (Auth::check()) {
            Auth::user()->read($thread);
        }

        $trending->push($thread);

        $thread->visits()->record();

        return view('threads.show', compact('thread'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Channel  $channel
     * @param  \App\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Channel $channel, Thread $thread, Request $request)
    {
        $this->authorize('update', $thread);

        return tap($thread)->update($request->validate([
            'title' => 'required',
            'body' => 'required',
        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Channel  $channel
     * @param  \App\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel, Thread $thread, Request $request)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if ($request->expectsJson()) {
            return Response::make([], 204);
        }

        return redirect()->route('threads');
    }

    /**
     * Return threads from given channel matching filters.
     *
     * @param  \App\Channel $channel
     * @param  \App\Filters\ThreadFilters $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->latest()->paginate(25);
    }
}
