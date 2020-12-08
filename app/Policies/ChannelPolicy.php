<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChannelPolicy
{
    use HandlesAuthorization;

    /**
     * Handle dynamic method calls into the policy.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     * @throws \Exception
     */
    public function __call(string $method, array $parameters)
    {
        if (! in_array($method, Channel::$permissions)) {
            throw new Exception("Unhandled channel permission [$method]");
        }

        return $this->hasChannelPermission($method, ...$parameters);
    }

    /**
     * Determine whether the user has the appropriate permission for the channel.
     *
     * @param  string  $permission
     * @param  \App\Models\User  $user
     * @param  \App\Models\Channel  $channel
     * @return mixed
     */
    protected function hasChannelPermission(string $permission, User $user, Channel $channel)
    {
        if (! $channel->isRestricted($permission)) {
            return true;
        }

        return $user->hasPermissionTo($channel->{"{$permission}Permission"});
    }
}
