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
     * Determine whether the user can update the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function update(User $user, Poll $poll)
    {
        return $poll->thread->user_id == $user->id && $poll->votes()->count() == 0 && (is_null($poll->locked_at) || $poll->locked_at > Carbon::now());
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
