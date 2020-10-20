<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserNovaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $signedInUser
     * @return mixed
     */
    public function viewAny(User $signedInUser)
    {
        return $this->view($signedInUser);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $signedInUser
     * @return mixed
     */
    public function view(User $signedInUser)
    {
        return $signedInUser->hasPermissionTo('users.view')
            || $this->update($signedInUser)
            || $this->delete($signedInUser);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $signedInUser
     * @return mixed
     */
    public function create(User $signedInUser)
    {
        return false;
    }

    /**
     * Determine whether the user can update the given profile.
     *
     * @param  \App\User  $signedInUser
     * @return mixed
     */
    public function update(User $signedInUser)
    {
        return $signedInUser->hasPermissionTo('users.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $signedInUser
     * @return mixed
     */
    public function delete(User $signedInUser)
    {
        return $signedInUser->hasPermissionTo('users.delete');
    }
}
