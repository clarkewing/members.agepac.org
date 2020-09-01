<?php

namespace App\Policies;

use App\User;
use App\Poll;
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
     * Determine whether the user can create polls.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
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
        return $poll->user_id == $user->id && empty($poll->votes());
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
}
