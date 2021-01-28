<?php

namespace App\Traits;

use App\Collections\RestrictedChannelsCollection;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;

trait RestrictedChannels
{
    /**
     * The "boot" method of the trait.
     *
     * @return void
     * @throws \Throwable
     */
    protected static function bootRestrictedChannels()
    {
        throw_unless(
            isset(static::$permissions),
            new Exception('Permissions array missing on model.')
        );
    }

    /**
     * Scope a query to only unrestricted channels.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $permission
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnrestricted(Builder $query, string $permission = null)
    {
        return $query->whereNotIn('slug', $this->getAllRestrictions($permission)->pluck('channel_slug'));
    }

    /**
     * Return a Collection with all the existing channel restrictions.
     *
     * @param  string|null  $permission
     * @return \Illuminate\Support\Collection
     */
    private function getAllRestrictions(string $permission = null): Collection
    {
        return Permission::where('name', 'LIKE', 'channels.' . ($permission ?? '%') . '.%')
            ->pluck('name')
            ->map($this->extractPermissionInfo());
    }

    /**
     * Extract info from permission name.
     *
     * @return \Closure
     */
    private function extractPermissionInfo(): \Closure
    {
        return function ($permissionName) {
            return [
                'permission_name' => $permissionName,
                'channel_slug' => Str::afterLast($permissionName, '.'),
                'permission' => Str::between($permissionName, '.', '.'),
            ];
        };
    }

    /**
     * Scope a query to only channels the user has permission for.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $permission
     * @param  \App\Models\User|null  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPermission(Builder $query, string $permission, User $user = null)
    {
        $user = $user ?? Auth::user();

        return $query->unrestricted($permission)
            ->orWhereIn(
                'slug',
                $this->getUserPermissions($user)
                    ->filter(function ($restriction) use ($permission) {
                        return $restriction['permission'] === $permission;
                    })
                    ->pluck('channel_slug')
            );
    }

    /**
     * Return a Collection with all the user's channel permissions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Support\Collection
     */
    private function getUserPermissions(User $user): Collection
    {
        return $user->getAllPermissions()
            ->pluck('name')
            ->filter(function ($permissionName) {
                return Str::startsWith($permissionName, 'channels.');
            })
            ->reject(function ($permissionName) {
                return $permissionName === 'channels.manage';
            })
            ->map($this->extractPermissionInfo());
    }

    /**
     * Create the associated permission model.
     *
     * @param  string  $permission
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createPermission(string $permission)
    {
        return Permission::create(['name' => $this->permissionName($permission)]);
    }

    /**
     * Return the permission name.
     *
     * @param  string  $permission
     * @return string
     */
    protected function permissionName(string $permission): string
    {
        return "channels.$permission.$this->slug";
    }

    /**
     * Delete the associated permission model.
     *
     * @param  string  $permission
     * @return void
     */
    public function deletePermission(string $permission)
    {
        $this->{"{$permission}Permission"}->delete();
    }

    /**
     * Determine if the channel is restricted.
     *
     * @param  string|array|null  $permissions
     * @return bool
     */
    public function isRestricted($permissions = null): bool
    {
        $permissions = is_null($permissions)
            ? static::$permissions
            : (array) $permissions;

        foreach ($permissions as $permission) {
            if (! is_null($this->findPermission($permission))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find the associated permission model.
     *
     * @param  string  $permission
     * @return string|null
     */
    protected function findPermission(string $permission)
    {
        try {
            return Permission::findByName($this->permissionName($permission));
        } catch (PermissionDoesNotExist $e) {
            return null;
        }
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        foreach (static::$permissions as $permission) {
            if (in_array($key, ["{$permission}Permission", "{$permission}_permission"], true)) {
                return $this->findPermission($permission);
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new RestrictedChannelsCollection($models);
    }
}
