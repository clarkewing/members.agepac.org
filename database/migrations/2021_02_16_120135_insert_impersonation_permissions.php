<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InsertImpersonationPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createPermission('impersonate');

        Role::findByName('Administrator')
            ->givePermissionTo('impersonate');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->deletePermission('impersonate');
    }

    /**
     * Create a permission.
     *
     * @param  string  $name
     * @return $this
     */
    protected function createPermission(string $name)
    {
        Permission::create(['name' => $name]);

        return $this;
    }

    /**
     * Create a permission.
     *
     * @param  string  $name
     * @return $this
     */
    protected function deletePermission(string $name)
    {
        Permission::where('name', $name)->delete();

        return $this;
    }
}
