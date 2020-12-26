<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return
            $this->delete($user)
            || $this->merge($user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view()
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasPermissionTo('companies.delete');
    }

    /**
     * Determine whether the user can merge models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function merge(User $user)
    {
        return $user->hasPermissionTo('companies.merge');
    }
}
