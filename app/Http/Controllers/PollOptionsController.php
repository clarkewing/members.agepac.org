<?php

namespace App\Http\Controllers;

use App\PollOption;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PollOptionsController extends Controller
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
        if ($poll) {
            return $poll->options()->get();
        } else {
            return [];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PollOption $pollOption)
    {
        $this->authorize('update', $pollOption);

        $request->validate(['label' => 'required',
            'color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i'], ]);

        $pollOption->update($request->only(['label', 'color']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, string $channelSlug, Thread $thread)
    {
        $poll = $thread->poll;

        if ($thread->locked) {
            return Response::make('Thread is locked.', 422);
        }

        $request->validate(['label' => 'required',
            'color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i'], ]);

        $pollOption = $poll->addOption([
            'label' => $request->input('label'),
            'color' => $request->input('color'),
        ]);

        return $pollOption;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PollOption  $pollOption
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, PollOption $pollOption)
    {
        $this->authorize('delete', $pollOption);

        $pollOption->delete();

        if ($request->expectsJson()) {
            return Response::make(['status' => 'Poll option deleted.']);
        }

        return back()
            ->with('flash', 'The poll option was deleted.');
    }
}
