<?php

namespace App\Policies;

use App\PollOption;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PollOptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create poll option.
     *
     * @param  \App\User  $user
     * @param  \App\PollOption  $pollOption
     * @return mixed
     */
    public function create(User $user, PollOption $pollOption)
    {
        return $this->update($user, $pollOption);
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
        return $pollOption->poll->thread->user_id === $user->id;
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
        return $this->update($user, $pollOption)
            && $pollOption->poll->options->count() > 2;
    }
}
