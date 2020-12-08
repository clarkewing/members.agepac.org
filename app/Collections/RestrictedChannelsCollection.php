<?php

namespace App\Collections;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;

class RestrictedChannelsCollection extends EloquentCollection
{
    /**
     * Return only channels that the user has a permission for.
     *
     * @param  string  $permission
     * @param  \App\Models\User|null  $user
     * @return $this
     */
    public function withPermission(string $permission, User $user = null)
    {
        $user = $user ?? Auth::user();

        return $this->filter(function ($channel) use ($permission, $user) {
            return $user->can($permission, $channel);
        });
    }
}
