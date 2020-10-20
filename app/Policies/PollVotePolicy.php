<?php

namespace App\Policies;

use App\Poll;
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
    public function create(User $user, Poll $poll)
    {
        return (is_null($poll->locked_at) || $poll->locked_at > Carbon::now()) && ($poll->votes()->where('user_id', $user->id)->count() < $poll->max_votes);
    }

    /**
     * Determine whether the user can view any vote.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function viewAny(User $user, Poll $poll)
    {
        switch ($poll->votes_privacy) {
            case 0:
                //No one can see who voted for what
                return false;
                break;
            case 1:
                //Only the vote creator can see who voted for what
                return $user->id == $poll->thread->user_id;
                break;
            default:
                //Everyone can see who voted for what
                return true;
                break;
        }
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
        $poll = $pollVote->poll;
        switch ($poll->votes_privacy) {
            case 0:
                //No one can see who voted for what
                return false;
                break;
            case 1:
                //Only the vote creator can see who voted for what
                return $user->id == $poll->thread->user_id;
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
        $poll = $pollVote->option->poll;
        return $pollVote->user_id == $user->id && $poll->votes_editable &&
               (is_null($poll->locked_at) || $poll->locked_at > Carbon::now());
    }

    /**
     * Determine whether the user can delete the vote.
     *
     * @param  \App\User  $user
     * @param  \App\PollVote  $pollVote
     * @return mixed
     */
    public function delete(User $user, PollVote $pollVote)
    {
        $poll = $pollVote->option->poll;
        return $pollVote->user_id == $user->id && $poll->votes_editable &&
               (is_null($poll->locked_at) || $poll->locked_at > Carbon::now());
    }
}
