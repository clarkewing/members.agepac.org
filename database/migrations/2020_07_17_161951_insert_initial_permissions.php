<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InsertInitialPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this
            // Users
            ->createSubjectPermissions('user_invitations', [
                'create',
                'edit',
                'delete',
            ])
            ->createSubjectPermissions('users', [
                'edit',
                'block',
                'unblock',
                'delete',
                'attachRoles',
                'detachRoles',
            ])
            ->createSubjectPermissions('subscriptions', [
                'manage',
            ])
            ->createSubjectPermissions('roles', [
                'create',
                'edit',
                'delete',
                'attachPermissions',
                'detachPermissions',
            ])
            ->createSubjectPermissions('permissions', [
                'view',
            ])

            // Profiles
            ->createSubjectPermissions('aircraft', [
                'edit',
                'delete',
            ])
            ->createSubjectPermissions('companies', [
                'delete',
            ])
            ->createSubjectPermissions('tags', [
                'edit',
                'delete',
            ])

            // Forum
            ->createSubjectPermissions('channels', [
                'create',
                'edit',
                'delete',
            ])
            ->createSubjectPermissions('threads', [
                'create',
                'edit',
                'delete',
                'viewDeleted',
                'restore',
                'forceDelete',
            ])
            ->createSubjectPermissions('posts', [
                'create',
                'edit',
                'delete',
                'viewDeleted',
                'restore',
                'forceDelete',
            ])

            // Content
            ->createSubjectPermissions('menu_items', [
                'create',
                'edit',
                'delete',
            ])
            ->createSubjectPermissions('pages', [
                'viewUnpublished',
                'create',
                'edit',
                'delete',
                'viewDeleted',
                'restore',
                'forceDelete',
            ]);

        Role::create(['name' => 'God with Wings'])
            ->syncPermissions(Permission::all());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        foreach (config('permission.table_names') as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Create permissions for a given subject.
     *
     * @param  string  $subject
     * @param  string[]  $permissions
     * @return \InsertInitialPermissions
     */
    protected function createSubjectPermissions(string $subject, array $permissions)
    {
        foreach ($permissions as $permission) {
            $this->createPermission("$subject.$permission");
        }

        return $this;
    }

    /**
     * Create a permission.
     *
     * @param  string  $name
     * @return \InsertInitialPermissions
     */
    protected function createPermission(string $name)
    {
        Permission::create(['name' => $name]);

        return $this;
    }
}
