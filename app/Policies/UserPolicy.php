<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the given profile.
     *
     * @param  \App\Models\User  $signedInUser
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function update(User $signedInUser, User $user)
    {
        return $signedInUser->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $signedInUser
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function delete(User $signedInUser, User $user)
    {
        return $signedInUser->id === $user->id;
    }
}
