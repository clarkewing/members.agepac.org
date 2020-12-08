<?php

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return mixed
     */
    public function update(User $user, Thread $thread)
    {
        return $thread->user_id === $user->id
            || $user->hasPermissionTo('threads.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return mixed
     */
    public function delete(User $user, Thread $thread)
    {
        if ($this->update($user, $thread)) {
            // A thread's creator can only delete their thread if it doesn't have any replies.
            return $thread->replies()->count() === 0;
        }

        return $user->hasPermissionTo('threads.delete');
    }

    /**
     * Determine whether the user can view deleted models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewDeleted(User $user)
    {
        return $user->hasPermissionTo('threads.viewDeleted')
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
        return $user->hasPermissionTo('threads.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return $user->hasPermissionTo('threads.forceDelete');
    }

    /**
     * Determine whether the user can lock the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return mixed
     */
    public function lock(User $user, Thread $thread)
    {
        return $thread->user_id === $user->id
               || $user->hasPermissionTo('threads.lock');
    }

    /**
     * Determine whether the user can unlock the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return mixed
     */
    public function unlock(User $user, Thread $thread)
    {
        return $thread->user_id === $user->id
               || $user->hasPermissionTo('threads.unlock');
    }

    /**
     * Determine whether the user can pin the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return mixed
     */
    public function pin(User $user, Thread $thread)
    {
        return $user->hasPermissionTo('threads.pin');
    }

    /**
     * Determine whether the user can unpin the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return mixed
     */
    public function unpin(User $user, Thread $thread)
    {
        return $user->hasPermissionTo('threads.unpin');
    }

    /**
     * Determine whether the user can attach a poll to the thread.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return mixed
     */
    public function attachPoll(User $user, Thread $thread)
    {
        return ! $thread->locked
               && ! $thread->hasPoll()
               && $this->update($user, $thread);
    }
}
