<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Reply;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string $channel
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function index(string $channelSlug, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreatePostRequest  $request
     * @param  string $channel
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePostRequest $request, string $channelSlug, Thread $thread)
    {
        if ($thread->locked) {
            return Response::make('Thread is locked.', 422);
        }

        return $thread->addReply([
            'body' => $request->input('body'),
            'user_id' => Auth::id(),
        ])->load('owner');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reply $reply)
    {
        $this->authorize('update', $reply);

        $request->validate(['body' => 'required']);

        $reply->update($request->only('body'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if ($request->expectsJson()) {
            return Response::make(['status' => 'Reply deleted']);
        }

        return back()
            ->with('flash', 'The reply was deleted.');
    }
}
