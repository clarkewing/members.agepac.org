<?php

namespace Tests\Feature\Nova;

use App\Models\UserInvitation;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageUserInvitationsTest extends TestCase
{
    use NovaTestRequests;

    public function permissionProvider()
    {
        return [
            'create' => ['create'],
            'edit' => ['edit'],
            'delete' => ['delete'],
        ];
    }

    public function modeProvider()
    {
        return [
            'create' => ['store', 'create'],
            'edit' => ['update', 'edit'],
        ];
    }

    /** @test */
    public function testUnauthorizedUsersCannotIndexUserInvitations()
    {
        $this->signIn();

        $this->indexResource('user-invitations')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewUserInvitations()
    {
        $userInvitation = UserInvitation::factory()->create();

        $this->signIn();

        $this->showResource('user-invitations', $userInvitation->id)
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotCreateUserInvitations()
    {
        $this->signIn();

        $this->storeUserInvitation()->assertForbidden();
        $this->assertDatabaseCount('user_invitations', 0);
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditUserInvitations()
    {
        $userInvitation = UserInvitation::factory()->create();

        $this->signIn();

        $this->updateUserInvitation(['first_name' => 'This name cannot exist'], $userInvitation)
            ->assertForbidden();

        $this->assertDatabaseMissing('user_invitations', [
            'id' => $userInvitation->id,
            'first_name' => 'This name cannot exist',
        ]);
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteUserInvitations()
    {
        $userInvitation = UserInvitation::factory()->create();

        $this->signIn();

        $this->deleteResource('user-invitations', $userInvitation->id);
        // Nova doesn't return 403 on unauthorized delete request, so we don't check the status.
        // Beware: with a random user, it'll return a 403 because of the viewAll authorization.
        // But with any other permission than delete, it would return a 200.

        $this->assertDatabaseCount('user_invitations', 1); // Ensure it wasn't deleted.
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanListUserInvitations($permission)
    {
        $this->signInWithPermission("user_invitations.$permission");

        $this->indexResource('user-invitations')
            ->assertOk();
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanViewUserInvitations($permission)
    {
        $userInvitation = UserInvitation::factory()->create();

        $this->signInWithPermission("user_invitations.$permission");

        $this->showResource('user-invitations', $userInvitation->id)
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanCreateUserInvitations()
    {
        $this->signInWithPermission('user_invitations.create');

        $this->storeUserInvitation(
            $userInvitation = UserInvitation::factory()->raw()
        )->assertCreated();

        $this->assertDatabaseHas('user_invitations', $userInvitation);
    }

    /** @test */
    public function testAuthorizedUsersCanEditUserInvitations()
    {
        $this->signInWithPermission('user_invitations.edit');

        $this->updateUserInvitation(
            $userInvitation = UserInvitation::factory()->raw()
        )->assertOk();

        $this->assertDatabaseHas('user_invitations', $userInvitation);
    }

    /** @test */
    public function testAuthorizedUsersCanDeleteUserInvitations()
    {
        $userInvitation = UserInvitation::factory()->create();

        $this->signInWithPermission('user_invitations.delete');

        $this->deleteResource('user-invitations', $userInvitation->id)
            ->assertOk();

        $this->assertDatabaseCount('user_invitations', 0);
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testInvitationRequiresAFirstName($verb, $permission)
    {
        $this->signInWithPermission("user_invitations.$permission");

        $this->{$verb.'UserInvitation'}(['first_name' => null])
            ->assertJsonValidationErrors('first_name');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testInvitationRequiresALastName($verb, $permission)
    {
        $this->signInWithPermission("user_invitations.$permission");

        $this->{$verb.'UserInvitation'}(['last_name' => null])
            ->assertJsonValidationErrors('last_name');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testInvitationRequiresAClassCourse($verb, $permission)
    {
        $this->signInWithPermission("user_invitations.$permission");

        $this->{$verb.'UserInvitation'}(['class_course' => null])
            ->assertJsonValidationErrors('class_course');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testInvitationMustHaveClassCourseRegisteredInConfig($verb, $permission)
    {
        $this->signInWithPermission("user_invitations.$permission");

        $this->{$verb.'UserInvitation'}(['class_course' => 'Foobar'])
            ->assertJsonValidationErrors('class_course');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testInvitationRequiresAClassYear($verb, $permission)
    {
        $this->signInWithPermission("user_invitations.$permission");

        $this->{$verb.'UserInvitation'}(['class_year' => null])
            ->assertJsonValidationErrors('class_year');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testInvitationClassYearMustBeAFourDigitYear($verb, $permission)
    {
        $this->signInWithPermission("user_invitations.$permission");

        $this->{$verb.'UserInvitation'}(['class_year' => 'Not a year'])
            ->assertJsonValidationErrors('class_year');

        $this->{$verb.'UserInvitation'}(['class_year' => '15'])
            ->assertJsonValidationErrors('class_year');
    }

    /**
     * Submits a request to create a user invitation.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function storeUserInvitation(array $overrides = [])
    {
        return $this->storeResource('user-invitations', array_merge(
            UserInvitation::factory()->raw(),
            $overrides
        ));
    }

    /**
     * Submits a request to update an existing user invitation.
     *
     * @param  array  $data
     * @param  \App\Models\UserInvitation|null  $userInvitation
     * @return \Illuminate\Testing\TestResponse
     */
    public function updateUserInvitation(array $data = [], UserInvitation $userInvitation = null)
    {
        $userInvitation = $userInvitation ?? UserInvitation::factory()->create();

        return $this->updateResource(
            'user-invitations', $userInvitation->id,
            array_merge($userInvitation->toArray(), $data)
        );
    }
}
