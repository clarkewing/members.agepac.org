<?php

namespace App;

use Exception;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;

trait ChannelPermissions
{
    /**
     * The "boot" method of the trait.
     *
     * @return void
     * @throws \Throwable
     */
    protected static function bootChannelPermissions()
    {
        throw_unless(
            isset(static::$permissions),
            new Exception('Permissions array missing on model.')
        );
    }

    /**
     * Create the associated permission model.
     *
     * @param  string  $permission
     * @return void
     */
    public function createPermission(string $permission)
    {
        Permission::create(['name' => $this->permissionName($permission)]);
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
     * Return the permission name.
     *
     * @param  string  $permission
     * @return string
     */
    protected function permissionName(string $permission): string
    {
        return "channels.$permission.$this->slug";
    }
}
