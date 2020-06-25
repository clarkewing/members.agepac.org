<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the profile.
     *
     * @param  \App\User  $user
     * @param  \App\User  $profile
     * @return mixed
     */
    public function update(User $user, User $profile)
    {
        return $profile->is($user);
    }
}
