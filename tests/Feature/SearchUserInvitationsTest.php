<?php

namespace Tests\Feature;

use App\UserInvitation;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SearchUserInvitationsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testGuestCanSearchInvitationsByFullNameOrAllParams()
    {
        $this->searchUserInvitations(['name' => 'John Doe'])
            ->assertOk();

        $this->searchUserInvitations([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'class_course' => 'EPL/S',
            'class_year' => '2015',
        ])->assertOk();
    }

    /** @test */
    public function testSearchIsThrottledToFiveMaxPerMinute()
    {
        $unthrottledAttempts = 5;

        for ($i = 0; $i < $unthrottledAttempts; $i++) {
            $this->searchUserInvitations(['name' => 'John Doe'])
                ->assertOk();
        }

        $this->searchUserInvitations(['name' => 'John Doe'])
            ->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }

    /** @test */
    public function testFullNameIsRequired()
    {
        $this->searchUserInvitations(['name' => null])
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function testFirstNameIsRequired()
    {
        $this->searchUserInvitations([
            'first_name' => null,
            'last_name' => 'Doe',
            'class_course' => 'EPL/S',
            'class_year' => '2015',
        ])->assertJsonValidationErrors('first_name');
    }

    /** @test */
    public function testLastNameIsRequired()
    {
        $this->searchUserInvitations([
            'first_name' => 'John',
            'last_name' => null,
            'class_course' => 'EPL/S',
            'class_year' => '2015',
        ])->assertJsonValidationErrors('last_name');
    }

    /** @test */
    public function testClassCourseIsRequired()
    {
        $this->searchUserInvitations([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'class_course' => null,
            'class_year' => '2015',
        ])->assertJsonValidationErrors('class_course');
    }

    /** @test */
    public function testClassCourseMustBeRegisteredInConfig()
    {
        $this->searchUserInvitations([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'class_course' => 'Foobar',
            'class_year' => '2015',
        ])->assertJsonValidationErrors('class_course');
    }

    /** @test */
    public function testClassYearIsRequired()
    {
        $this->searchUserInvitations([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'class_course' => 'EPL/S',
            'class_year' => null,
        ])->assertJsonValidationErrors('class_year');
    }

    /** @test */
    public function testClassYearMustBeFourDigitYear()
    {
        $this->searchUserInvitations([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'class_course' => 'EPL/S',
            'class_year' => 'not-a-year',
        ])->assertJsonValidationErrors('class_year');

        $this->searchUserInvitations([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'class_course' => 'EPL/S',
            'class_year' => 12,
        ])->assertJsonValidationErrors('class_year');
    }

    /** @test */
    public function testGuestCanSearchForAnInvitationByFullName()
    {
        create(UserInvitation::class, ['first_name' => 'John', 'last_name' => 'Doe']);
        create(UserInvitation::class, ['first_name' => 'Jane', 'last_name' => 'Doe']);
        create(UserInvitation::class, ['first_name' => 'Hugo', 'last_name' => 'Clarke-Wing']);

        $this->searchUserInvitations(['name' => 'Mr. Nobody'])
            ->assertJsonCount(0);

        $this->searchUserInvitations(['name' => 'john doe'])
            ->assertJson([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]);
    }

    /** @test */
    public function testGuestCanSearchForAnInvitationByFullParams()
    {
        $invite = create(UserInvitation::class);

        $this->searchUserInvitations([
            'first_name' => 'Arnold',
            'last_name' => 'Nobody',
            'class_course' => 'EPL/S',
            'class_year' => 1901,
        ])->assertJsonCount(0);

        $this->searchUserInvitations($invite->toArray())
            ->assertJson([
                'first_name' => $invite->first_name,
                'last_name' => $invite->last_name,
            ]);
    }

    /**
     * Submits a post request to create a user invitation.
     *
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    public function searchUserInvitations(array $data)
    {
        return $this->getJson(route('api.user-invitations.index', $data));
    }
}
