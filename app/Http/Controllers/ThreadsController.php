<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadFilters;
use App\Thread;
use App\Trending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

class ThreadsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('verified:threads.index,Tu dois vérifier ton adresse email avant de pouvoir publier.')
            ->only(['create', 'store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Channel|null  $channel
     * @param  \App\Filters\ThreadFilters  $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Channel $channel, ThreadFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters);

        if ($request->expectsJson()) {
            return $threads;
        }

        return view('threads.index', compact('threads'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'channel_id' => [
                'required',
                Rule::in(Channel::pluck('id')),
            ],
        ]);

        $thread = Thread::create([
            'user_id' => Auth::id(),
            'channel_id' => $request->input('channel_id'),
            'title' => $request->input('title'),
        ]);

        $thread->addPost([
            'user_id' => Auth::id(),
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
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @param  \App\Trending  $trending
     * @return \Illuminate\Http\Response
     */
    public function show(string $channelSlug, Thread $thread, Trending $trending)
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
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $channelSlug, Thread $thread)
    {
        $this->authorize('update', $thread);

        return tap($thread)->update($request->validate([
            'title' => 'required',
        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $channelSlug
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, string $channelSlug, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if ($request->expectsJson()) {
            return Response::make([], 204);
        }

        return redirect()->route('threads.index');
    }

    /**
     * Return threads from given channel matching filters.
     *
     * @param  \App\Channel  $channel
     * @param  \App\Filters\ThreadFilters  $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::orderBy('pinned', 'desc')
            ->latest()
            ->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);

            View::share(['channel' => $channel]);
        }

        return $threads->paginate(25);
    }
}
