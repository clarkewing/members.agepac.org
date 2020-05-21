<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AccountTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testGuestsCannotSeePageToEditAccount()
    {
        Auth::logout();

        $this->get(route('account.edit'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUserCanViewThePageToEditTheirAccountInfo()
    {
        $this->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Mes informations');
    }

    /** @test */
    public function testUserCanViewTheirCurrentInfoOnEditPage()
    {
        tap(Auth::user(), function ($user) {
            $this->get(route('account.edit'))
                ->assertSee($user->first_name)
                ->assertSee($user->last_name)
                ->assertSee($user->birthdate)
                ->assertSee($user->phone)
                ->assertSee($user->email);
        });
    }

    /** @test */
    public function testGuestCannotUpdateAccountInfo()
    {
        Auth::logout();

        $this->patchJson(route('account.update'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function testUserCannotChangeName()
    {
        Auth::logout();
        $this->signIn(create(User::class, [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]));

        $this->updateAccount(['first_name' => 'Juliette'])->assertJsonValidationErrors('first_name');

        $this->assertEquals('John', Auth::user()->first_name);

        $this->updateAccount(['last_name' => 'Loiseau'])->assertJsonValidationErrors('last_name');

        $this->assertEquals('Doe', Auth::user()->last_name);
    }

    /** @test */
    public function testBirthdateMustBeADate()
    {
        $this->updateAccount(['birthdate' => 'not-a-date'])
            ->assertJsonValidationErrors('birthdate');

        $this->updateAccount(['birthdate' => '1990-02-31'])
            ->assertJsonValidationErrors('birthdate');
    }

    /** @test */
    public function testMustBeOlderThanThirteen()
    {
        $this->updateAccount(['birthdate' => now()->subYears(10)->toDateString()])
            ->assertJsonValidationErrors('birthdate');
    }

    /** @test */
    public function testUserCanUpdateTheirBirthdate()
    {
        $this->updateAccount(['birthdate' => '1994-09-22'])->assertOk();

        $this->assertEquals('1994-09-22', Auth::user()->birthdate);
    }

    /** @test */
    public function testPhoneMustBeAValidNumber()
    {
        $this->updateAccount(['phone' => 'n0t_4_ph0n3_numb3r'])
            ->assertJsonValidationErrors('phone');
    }

    /** @test */
    public function testPhoneCountryCanBeAutodetectedFromCountryCode()
    {
        $this->updateAccount(['phone' => '+1 (202) 456-1414']) // White House
        ->assertSessionDoesntHaveErrors();

        $this->assertEquals('US', Auth::user()->phone->getCountry());
    }

    /** @test */
    public function testPhoneWithoutCountryCodeFallsbackToFranceThenGeoIpBased()
    {
        // US IP address (using Google data)
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8']);

        $this->updateAccount(['phone' => '05 62 17 40 00']) // ENAC Toulouse number
            ->assertJsonMissingValidationErrors();

        $this->assertEquals('FR', Auth::user()->phone->getCountry());

        $this->updateAccount(['phone' => '(650) 253-0000']) // Google California number
            ->assertJsonMissingValidationErrors();

        $this->assertEquals('US', Auth::user()->phone->getCountry());

        $this->updateAccount(['phone' => '0370 010 0222']) // BBC London number
            ->assertJsonValidationErrors('phone');
    }

    /** @test */
    public function testUserCanUpdateTheirPhone()
    {
        $this->updateAccount(['phone' => '+33 1 41 41 12 34'])->assertOk();

        $this->assertEquals('+33 1 41 41 12 34', Auth::user()->phone->formatInternational());
    }

    /** @test */
    public function testCurrentPasswordRequiredToChangeEmail()
    {
        $this->updateAccount(['email' => 'new@example.com'])
            ->assertJsonValidationErrors('current_password');
    }

    /** @test */
    public function testCurrentPasswordNotRequiredIfEmailUnchanged()
    {
        $this->updateAccount(['email' => Auth::user()->email])
            ->assertJsonMissingValidationErrors();
    }

    /** @test */
    public function testEmailMustBeValid()
    {
        $this->updateAccount([
            'email' => 'not-an-email',
            'current_password' => 'password',
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    public function testEmailMustBeUnique()
    {
        create(User::class, ['email' => 'existing@email.com']);

        $this->updateAccount([
            'email' => 'existing@email.com',
            'current_password' => 'password',
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    public function testCurrentEmailIsAccepted()
    {
        Auth::logout();
        $this->signIn(create(User::class, ['email' => 'john@supercool.com']));

        $this->updateAccount([
            'email' => 'john@supercool.com',
            'current_password' => 'password',
        ])->assertJsonMissingValidationErrors();
    }

    /** @test */
    public function testUserCanUpdateEmail()
    {
        $this->updateAccount([
            'email' => 'newinbox@example.com',
            'current_password' => 'password',
        ])->assertOk();

        $this->assertEquals('newinbox@example.com', Auth::user()->email);
    }

    /** @test */
    public function testChangedEmailResetsVerification()
    {
        $this->updateAccount([
            'email' => 'newinbox@example.com',
            'current_password' => 'password',
        ])->assertOk();

        $this->assertNull(Auth::user()->email_verified_at);
    }

    /** @test */
    public function testUnchangedEmailDoesNotTriggerVerificationNotification()
    {
        Notification::fake();

        $this->updateAccount([
            'email' => Auth::user()->email,
            'current_password' => 'password',
        ]);

        Notification::assertNothingSent();
    }

    /** @test */
    public function testEmailUpdateTriggersVerificationNotification()
    {
        Notification::fake();

        $this->updateAccount([
            'email' => 'newinbox@example.com',
            'current_password' => 'password',
        ]);

        Notification::assertSentTo(
            [Auth::user()], VerifyEmail::class
        );
    }

    /** @test */
    public function testCurrentPasswordMustMatch()
    {
        $this->updateAccount(['current_password' => 'wrongpassword'])
            ->assertJsonValidationErrors('current_password');
    }

    /** @test */
    public function testCurrentPasswordRequiredToChangePassword()
    {
        $this->updateAccount([
            'new_password' => 'SoSafeYouCantBelieveIt',
            'new_password_confirmation' => 'SoSafeYouCantBelieveIt',
        ])->assertJsonValidationErrors('current_password');
    }

    /** @test */
    public function testNewPasswordMustBeLongerThanEightCharacters()
    {
        $this->updateAccount([
            'current_password' => 'password',
            'new_password' => 'foo',
            'new_password_confirmation' => 'foo',
        ])->assertJsonValidationErrors('new_password');
    }

    /** @test */
    public function testNewPasswordMustBeLongerConfirmed()
    {
        $this->updateAccount([
            'current_password' => 'password',
            'new_password' => 'coolpass',
            'new_password_confirmation' => 'c00lpass',
        ])->assertJsonValidationErrors('new_password');
    }

    /** @test */
    public function testUserCanUpdatePassword()
    {
        $this->updateAccount([
            'current_password' => 'password',
            'new_password' => 'newpassword',
            'new_password_confirmation' => 'newpassword',
        ])->assertOk();

        $this->assertTrue(Hash::check('newpassword', Auth::user()->password));
    }

    /**
     * Submits a patch request to update an account.
     *
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    public function updateAccount(array $data = [])
    {
        return $this->patchJson(route('account.update'), $data);
    }
}
