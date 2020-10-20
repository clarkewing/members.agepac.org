<?php

namespace App\Policies;

use App\PollVote;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Carbon;

class PollVotePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can cast a vote.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, PollVote $pollVote)
    {
        return is_null($pollVote->poll->locked_at) || $pollVote->poll->locked_at < Carbon::now();
    }

    /**
     * Determine whether the user can view the vote.
     *
     * @param  \App\User  $user
     * @param  \App\PollVote  $pollVote
     * @return mixed
     */
    public function view(User $user, PollVote $pollVote)
    {
        $poll = $pollVote->poll();
        switch ($poll->votes_privacy) {
            case 0:
                //No one can see who voted for what
                return false;
                break;
            case 1:
                //Only the vote creator can see who voted for what
                return $user->id == $poll->user_id;
                break;
            default:
                //Everyone can see who voted for what
                return true;
                break;
        }
    }

    /**
     * Determine whether the user can update the vote.
     *
     * @param  \App\User  $user
     * @param  \App\PollVote  $pollVote
     * @return mixed
     */
    public function update(User $user, PollVote $pollVote)
    {
        return $pollVote->user_id == $user->id && $pollVote->poll->votes_editable &&
               (is_null($pollVote->poll->locked_at) || $pollVote->poll->locked_at < Carbon::now());
    }
}
