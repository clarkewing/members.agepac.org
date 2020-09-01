<?php

namespace App\Policies;

use App\Poll;
use App\PollOption;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PollOptionPolicy
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
     * Determine whether the user can create poll option.
     *
     * @param  \App\User  $user
     * @param  \App\PollOption  $pollOption
     * @return mixed
     */
    public function create(User $user, PollOption $pollOption)
    {
        return $pollOption->poll->user_id == $user->id;
    }

    /**
     * Determine whether the user can update the poll option.
     *
     * @param  \App\User  $user
     * @param  \App\PollOption  $pollOption
     * @return mixed
     */
    public function update(User $user, PollOption $pollOption)
    {
        return $pollOption->poll->user_id == $user->id;
    }

        /**
     * Determine whether the user can delete the poll option.
     *
     * @param  \App\User  $user
     * @param  \App\PollOption  $pollOption
     * @return mixed
     */
    public function delete(User $user, PollOption $pollOption)
    {
        return $pollOption->poll->user_id == $user->id;
    }
}
