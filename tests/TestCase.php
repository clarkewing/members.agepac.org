<?php

namespace Tests;

use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;
use ClarkeWing\LegacySync\Facades\LegacySync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('PRAGMA foreign_keys=on;');

        $this->withoutExceptionHandling();

        Mailcoach::fake();

        LegacySync::fake();
    }

    /**
     * Sign in with a user.
     *
     * @param  \App\Models\User|null $user
     * @return $this
     */
    protected function signIn(?User $user = null)
    {
        $user = $user ?? User::factory()->create();

        $this->be($user);

        return $this;
    }

    /**
     * Sign in with a user without a subscription.
     *
     * @return $this
     */
    protected function signInUnsubscribed()
    {
        return $this->signIn(
            User::factory()->withoutSubscription()->create()
        );
    }

    /**
     * Sign in with a user and give it a permission.
     *
     * @param  string|string[]  $permission
     * @return $this
     */
    protected function signInWithPermission($permission)
    {
        return $this->signIn(
            User::factory()->create()
                ->givePermissionTo($permission)
        );
    }

    /**
     * Sign in with a user and give it a role.
     *
     * @param  string|string[]  $role
     * @return $this
     */
    protected function signInWithRole($role)
    {
        return $this->signIn(
            User::factory()->create()
                ->assignRole($role)
        );
    }
}
