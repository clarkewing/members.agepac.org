<?php

namespace App\Policies;

use App\Exceptions\ThrottleException;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     * @throws \App\Exceptions\ThrottleException
     */
    public function create(User $user)
    {
        if (optional($user->fresh()->lastPost)->wasJustPublished()) {
            throw new ThrottleException('Du calme moussaillon. Tu postes beaucoup, prends une petite pause.');
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        return $this->postIsOwnedBy($post, $user)
               || $user->hasPermissionTo('posts.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        return ! $post->is_thread_initiator
            && (
                $this->postIsOwnedBy($post, $user)
                   || $user->hasPermissionTo('posts.delete')
            );
    }

    /**
     * Determine whether the user can view deleted models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewDeleted(User $user)
    {
        return $user->hasPermissionTo('posts.viewDeleted')
               || $this->restore($user)
               || $this->forceDelete($user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function restore(User $user)
    {
        return $user->hasPermissionTo('posts.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return $user->hasPermissionTo('posts.forceDelete');
    }

    /**
     * Determine whether the given post is owned by the given user.
     *
     * @param  \App\Models\Post  $post
     * @param  \App\Models\User  $user
     * @return bool
     */
    protected function postIsOwnedBy(Post $post, User $user): bool
    {
        return $post->user_id === $user->id;
    }
}
