<?php

namespace Tests\Feature;

use App\User;
use App\UserInvitation;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        Notification::fake();
    }

    /** @test */
    public function testGuestCanRegisterAnAccount()
    {
        $this->createAccount([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'HugoWasHere',
            'password_confirm' => 'HugoWasHere',
        ])->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->assertCount(1, User::all());

        tap(User::first(), function ($user) {
            $this->assertEquals('John', $user->first_name);
            $this->assertEquals('Doe', $user->last_name);
            $this->assertEquals('john@example.com', $user->email);
            $this->assertTrue(Hash::check('HugoWasHere', $user->password));
        });
    }

    /** @test */
    public function testUserMustBeInvitedToRegister()
    {
        $this->createAccount([], $invited = false)
            ->assertSessionHasErrors('invitation');
    }

    /** @test */
    public function testUserInvitationIsDeletedAfterRegistration()
    {
        $userInvitation = create(UserInvitation::class);
        $this->assertDatabaseHas('user_invitations', ['id' => $userInvitation->id]);

        $this->createAccount(Arr::only(
            $userInvitation->toArray(),
            ['first_name', 'last_name', 'class_course', 'class_year']
        ));

        $this->assertDatabaseMissing('user_invitations', ['id' => $userInvitation->id]);
    }

    /** @test */
    public function testConfirmationEmailSentUponRegistration()
    {
        $this->createAccount();

        Notification::assertSentTo(
            [Auth::user()], VerifyEmail::class
        );
    }

    /** @test */
    public function testNewUserCanVerifyTheirEmail()
    {
        $this->createAccount();

        $user = Auth::user();
        $this->flushSession();

        $this->assertNull($user->email_verified_at);

        $this->get((new VerifyEmail)->toMail($user)->actionUrl)
            ->assertRedirect(route('home'));

        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function testFirstNameIsRequired()
    {
        $this->createAccount(['first_name' => ''])
            ->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function testFirstNameCannotExceed255Characters()
    {
        $this->createAccount(['first_name' => str_repeat('a', 256)])
            ->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function testLastNameIsRequired()
    {
        $this->createAccount(['last_name' => ''])
            ->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function testLastNameCannotExceed255Characters()
    {
        $this->createAccount(['last_name' => str_repeat('a', 256)])
            ->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function testEmailIsRequired()
    {
        $this->createAccount(['email' => ''])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testEmailCannotExceed255Characters()
    {
        $this->createAccount(['email' => str_repeat('a', 256) . '@example.com'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testEmailMustBeValid()
    {
        $this->createAccount(['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testEmailMustBeUnique()
    {
        create(User::class, ['email' => 'john@example.com']);

        $this->createAccount(['email' => 'john@example.com'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testPasswordIsRequired()
    {
        $this->createAccount(['password' => ''])
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function testPasswordMustBeLongerThan8Characters()
    {
        $this->createAccount(['password' => 'foobar'])
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function testPasswordMustBeConfirmed()
    {
        $this->createAccount(['password_confirmation' => 'oopswrong'])
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function testClassCourseIsRequired()
    {
        $this->createAccount(['class_course' => null])
            ->assertSessionHasErrors('class_course');
    }

    /** @test */
    public function testClassCourseMustBeRegisteredInConfig()
    {
        $this->createAccount(['class_course' => 'Foobar'])
            ->assertSessionHasErrors('class_course');
    }

    /** @test */
    public function testClassYearIsRequired()
    {
        $this->createAccount(['class_year' => null])
            ->assertSessionHasErrors('class_year');
    }

    /** @test */
    public function testClassYearMustBeFourDigitYear()
    {
        $this->createAccount(['class_year' => 'not-a-year'])
            ->assertSessionHasErrors('class_year');

        $this->createAccount(['class_year' => 12])
            ->assertSessionHasErrors('class_year');
    }

    /** @test */
    public function testGenderIsRequired()
    {
        $this->createAccount(['gender' => null])
            ->assertSessionHasErrors('gender');
    }

    /** @test */
    public function testGenderMustBeRegisteredInConfig()
    {
        $this->createAccount(['gender' => 'Z'])
            ->assertSessionHasErrors('gender');
    }

    /** @test */
    public function testBirthdateIsRequired()
    {
        $this->createAccount(['birthdate' => null])
            ->assertSessionHasErrors('birthdate');
    }

    /** @test */
    public function testBirthdateMustBeAValidDate()
    {
        $this->createAccount(['birthdate' => 'not-a-date'])
            ->assertSessionHasErrors('birthdate');

        $this->createAccount(['birthdate' => '1990-02-31'])
            ->assertSessionHasErrors('birthdate');
    }

    /** @test */
    public function testBirthdateMustBeOfIso8601Format()
    {
        $this->createAccount(['birthdate' => '22/09/1994'])
            ->assertSessionHasErrors('birthdate');
    }

    /** @test */
    public function testUserMustBeOlderThanThirteen() // Plenty of margin there...
    {
        $this->createAccount(['birthdate' => now()->subYears(10)->toDateString()])
            ->assertSessionHasErrors('birthdate');
    }

    /** @test */
    public function testPhoneIsRequired()
    {
        $this->createAccount(['phone' => null])
            ->assertSessionHasErrors('phone');
    }

    /** @test */
    public function testPhoneMustBeAValidNumber()
    {
        $this->createAccount(['phone' => 'n0t_4_ph0n3_numb3r'])
            ->assertSessionHasErrors('phone');
    }

    /** @test */
    public function testPhoneCountryCanBeAutodetectedFromCountryCode()
    {
        $this->createAccount(['phone' => '+1 (202) 456-1414']) // White House
        ->assertSessionDoesntHaveErrors();

        $this->assertEquals('US', Auth::user()->phone->getCountry());
    }

    /**
     * @test
     * @group external-api
     * @group geoip-api
     */
    public function testPhoneWithoutCountryCodeFallsbackToFranceThenGeoIpBased()
    {
        // US IP address (using Google data)
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8']);

        $this->createAccount(['phone' => '05 62 17 40 00']) // ENAC Toulouse number
        ->assertSessionDoesntHaveErrors();

        $this->assertEquals('FR', Auth::user()->phone->getCountry());

        Auth::logout();

        $this->createAccount(['phone' => '(650) 253-0000']) // Google California number
        ->assertSessionDoesntHaveErrors();

        $this->assertEquals('US', Auth::user()->phone->getCountry());

        Auth::logout();

        $this->createAccount(['phone' => '0370 010 0222']) // BBC London number
        ->assertSessionHasErrors('phone');
    }

    /**
     * Submits a post request to create an account.
     *
     * @param  array  $overrides
     * @param  bool  $invited
     * @return \Illuminate\Testing\TestResponse
     */
    public function createAccount(array $overrides = [], bool $invited = true)
    {
        $userData = array_merge(
            Arr::only(make(User::class)->getAttributes(), [
                'first_name',
                'last_name',
                'email',
                'class_course',
                'class_year',
                'gender',
                'birthdate',
                'phone',
            ]),
            ['password' => 'HugoWasHere', 'password_confirmation' => 'HugoWasHere']
        );

        if ($invited) {
            $this->inviteUser($userData, $overrides);
        }

        return $this->post(route('register'), array_merge(
            $userData,
            $overrides
        ));
    }

    /**
     * Creates a UserInvitation with the given data.
     *
     * @param  array  $userData
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function inviteUser(array $userData, array $overrides = [])
    {
        return create(UserInvitation::class, Arr::only(
            array_merge(
                $userData,
                array_filter($overrides) // Only override values that aren't null for invitation
            ),
            ['first_name', 'last_name', 'class_course', 'class_year']
        ));
    }
}
