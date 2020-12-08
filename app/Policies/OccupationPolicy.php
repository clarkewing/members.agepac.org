<?php

namespace App\Policies;

use App\Models\Occupation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OccupationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the occupation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Occupation  $occupation
     * @return bool
     */
    public function update(User $user, Occupation $occupation): bool
    {
        return $occupation->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the occupation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Occupation  $occupation
     * @return bool
     */
    public function delete(User $user, Occupation $occupation): bool
    {
        return $this->update($user, $occupation);
    }
}
