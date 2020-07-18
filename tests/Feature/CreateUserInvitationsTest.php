<?php

namespace Tests\Feature;

use App\UserInvitation;
use Tests\NovaTestCase;

class CreateUserInvitationsTest extends NovaTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signInGod();

    }

    /** @test */
    public function testCanInviteUsers()
    {
        $this->createUserInvitation(
            $userInvitation = make(UserInvitation::class)->toArray()
        )->assertCreated();

        $this->assertDatabaseHas('user_invitations', $userInvitation);
    }

    /** @test */
    public function testInvitationRequiresAFirstName()
    {
        $this->createUserInvitation(['first_name' => null])
            ->assertJsonValidationErrors('first_name');
    }

    /** @test */
    public function testInvitationRequiresALastName()
    {
        $this->createUserInvitation(['last_name' => null])
            ->assertJsonValidationErrors('last_name');
    }

    /** @test */
    public function testInvitationRequiresAClassCourse()
    {
        $this->createUserInvitation(['class_course' => null])
            ->assertJsonValidationErrors('class_course');
    }

    /** @test */
    public function testInvitationMustHaveClassCourseRegisteredInConfig()
    {
        $this->createUserInvitation(['class_course' => 'Foobar'])
            ->assertJsonValidationErrors('class_course');
    }

    /** @test */
    public function testInvitationRequiresAClassYear()
    {
        $this->createUserInvitation(['class_year' => null])
            ->assertJsonValidationErrors('class_year');
    }

    /** @test */
    public function testInvitationClassYearMustBeAFourDigitYear()
    {
        $this->createUserInvitation(['class_year' => 'Not a year'])
            ->assertJsonValidationErrors('class_year');

        $this->createUserInvitation(['class_year' => '15'])
            ->assertJsonValidationErrors('class_year');
    }

    /**
     * Submits a post request to create a user invitation.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function createUserInvitation(array $overrides = [])
    {
        return $this->storeResource('user-invitations', array_merge(
            make(UserInvitation::class)->toArray(),
            $overrides
        ));
    }
}
