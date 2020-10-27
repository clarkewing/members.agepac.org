<?php

namespace App\Policies;

use App\Poll;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PollPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function update(User $user, Poll $poll)
    {
        return ! $poll->isLocked()
               && ! $poll->has_votes
               && ! $poll->thread->locked
               && $user->can('update', $poll->thread);
    }

    /**
     * Determine whether the user can delete the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function delete(User $user, Poll $poll)
    {
        return ! $poll->thread->locked
               && $user->can('update', $poll->thread);
    }

    /**
     * Determine whether the user can vote in the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function vote(User $user, Poll $poll)
    {
        return (is_null($poll->locked_at) || $poll->locked_at > now())
               && (! $poll->hasVoted($user) || $poll->votes_editable);
    }

    /**
     * Determine whether the user can view the poll result.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function viewResults(User $user, Poll $poll)
    {
        return $user->can('update', $poll->thread)
               || $poll->results_before_voting || $poll->hasVoted();
    }

    /**
     * Determine whether the user can view the poll votes.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function viewVotes(User $user, Poll $poll)
    {
        switch (Poll::$votesPrivacyValues[$poll->votes_privacy]) {
            case 'public':
                return true;

            case 'private':
                return $user->can('update', $poll->thread);

            case 'anonymous':
            default:
                return false;
        }
    }
}
