<?php

namespace App\Http\Controllers;

use App\Poll;
use App\PollOption;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
    public function index(Poll $poll)
    {
        return $poll->votes();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Poll $poll, PollOption $pollOption)
    {
        if ($poll->locked_at != null) {
            return Response::make('Poll is locked.', 422);
        }

        return $poll->addVote([
            'option_id' => $pollOption->id,
            'user_id' => Auth::id(),
        ]);;
    }
}
