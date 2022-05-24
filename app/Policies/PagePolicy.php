<?php

namespace App\Policies;

use App\Exceptions\UnsubscribedException;
use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\AuthenticationException;

class PagePolicy
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
        return $this->create($user)
            || $this->update($user)
            || $this->delete($user)
            || $this->restore($user)
            || $this->forceDelete($user)
            || $this->viewDeleted($user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User|null  $user
     * @param  \App\Models\Page  $page
     * @return bool
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \App\Exceptions\UnsubscribedException
     */
    public function view(?User $user, Page $page): bool
    {
        if ($page->restricted) {
            if (is_null($user)) {
                throw new AuthenticationException;
            }

            if (! $user->subscribed('default')) {
                throw new UnsubscribedException;
            }
        }

        if ($page->trashed()) {
            if (! is_null($user)) {
                return $this->viewDeleted($user);
            }

            return false;
        }

        if (is_null($page->published_at) || $page->published_at->isFuture()) {
            return $user?->hasPermissionTo('pages.viewUnpublished') === true;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('pages.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasPermissionTo('pages.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasPermissionTo('pages.delete');
    }

    /**
     * Determine whether the user can view deleted models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewDeleted(User $user)
    {
        return $user->hasPermissionTo('pages.viewDeleted');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function restore(User $user)
    {
        return $user->hasPermissionTo('pages.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return $user->hasPermissionTo('pages.forceDelete');
    }
}
