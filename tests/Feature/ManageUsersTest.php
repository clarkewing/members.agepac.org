<?php

namespace Tests\Feature;

use App\User;
use Tests\NovaTestCase;

class ManageUsersTest extends NovaTestCase
{
    public function permissionProvider()
    {
        return [
            'view' => ['view'],
            'edit' => ['edit'],
            'delete' => ['delete'],
        ];
    }

    /** @test */
    public function testUnauthorizedUsersCannotIndexUsers()
    {
        $this->signIn();

        $this->indexResource('users')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewUsers()
    {
        $user = create(User::class);

        $this->signIn();

        $this->showResource('users', $user->id)
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditUsers()
    {
        $user = create(User::class);

        $this->signIn();

        $this->updateUser(['first_name' => 'This name cannot exist'], $user)
            ->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'first_name' => 'This name cannot exist',
        ]);
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteUsers()
    {
        $user = create(User::class);

        $this->signIn();

        $this->deleteResource('users', $user->id);
        // Nova doesn't return 403 on unauthorized delete request, so we don't check the status.
        // Beware: with a random user, it'll return a 403 because of the viewAll authorization.
        // But with any other permission than delete, it would return a 200.

        $this->assertDatabaseCount('users', 2); // Ensure it wasn't deleted.
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanListUsers($permission)
    {
        $this->signInWithPermission("users.$permission");

        $this->indexResource('users')
            ->assertOk();
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanViewUsers($permission)
    {
        $user = create(User::class);

        $this->signInWithPermission("users.$permission");

        $this->showResource('users', $user->id)
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanEditUsers()
    {
        $this->signInWithPermission('users.edit');

        $this->updateUser(
            $user = make(User::class)
                ->makeVisible('email')
                ->makeHidden(['name', 'isAdmin'])
                ->toArray()
        )->assertOk();

        $this->assertDatabaseHas('users', $user);
    }

    /** @test */
    public function testAuthorizedUsersCanDeleteUsers()
    {
        $user = create(User::class);

        $this->signInWithPermission('users.delete');

        $this->deleteResource('users', $user->id)
            ->assertOk();

        $this->assertDatabaseCount('users', 1); // The currently signed in user
    }

    /** @test */
    public function testCreatingUsersIsForbidden()
    {
        $this->signIn();

        $this->storeResource('users', make(User::class)->toArray())
            ->assertForbidden();

        $this->assertDatabaseCount('users', 1); // The currently signed in user
    }

    /**
     * Submits a request to update an existing user invitation.
     *
     * @param  array  $data
     * @param  \App\User|null  $user
     * @return \Illuminate\Testing\TestResponse
     */
    public function updateUser(array $data = [], User $user = null)
    {
        $user = $user ?? create(User::class);

        return $this->updateResource(
            'users', $user->id,
            array_merge($user->makeVisible('email')->toArray(), $data)
        );
    }
}
