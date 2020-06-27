<?php

namespace App\Policies;

use App\Occupation;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OccupationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the occupation.
     *
     * @param  \App\User  $user
     * @param  \App\Occupation  $occupation
     * @return bool
     */
    public function update(User $user, Occupation $occupation): bool
    {
        return $occupation->user_id === $user->id;
    }
}
