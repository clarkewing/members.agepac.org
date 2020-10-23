<?php

namespace App\Policies;

use App\Poll;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PollPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any polls.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function view(User $user, Poll $poll)
    {
        //
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
     * Determine whether the user can view the poll result.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function viewVotes(User $user, Poll $poll)
    {
        switch (Poll::$votesPrivacyValues[$poll->votes_privacy]) {
            case 'public':
                // Everyone can see who voted for what.
                return true;
                break;

            case 'private':
                // Only those with poll update abilities can see who voted for what.
                return $user->can('update', $poll->thread);
                break;

            case 'anonymous':
            default:
                // No one can see who voted for what.
                return false;
                break;
        }
    }

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
               && $poll->votes()->count() === 0
               && ! $poll->thread->locked
               && $user->can('update', $poll->thread);
    }

    /**
     * Determine whether the user can lock the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function lock(User $user, Poll $poll)
    {
        return $poll->thread->user_id == $user->id && (is_null($poll->locked_at) || $poll->locked_at > Carbon::now());
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
        return ! $poll->user_id
               && $this->update($user, $poll);
    }

    /**
     * Determine whether the user can restore the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function restore(User $user, Poll $poll)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function forceDelete(User $user, Poll $poll)
    {
        //
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
        $userVotesCount = $poll->votes()->where('user_id', $user->id)->count();

        return (is_null($poll->locked_at) || $poll->locked_at > now())
               && ($userVotesCount === 0 || $poll->votes_editable);
    }
}
