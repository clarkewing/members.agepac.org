<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Livewire\Migrate;
use App\Http\Livewire\Register;
use App\Models\User;
use App\Notifications\VerificationToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

class MigrateUserTest extends TestCase
{
    /** @var \App\Models\User */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => null,
        ]);
    }

    /** @test */
    public function testEmailIsRequiredToMigrate()
    {
        $this->post(action([LoginController::class, 'login']), [
            'email' => null,
        ])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function testPasswordIsNotRequiredToMigrate()
    {
        $this->post(action([LoginController::class, 'login']), [
            'email' => $this->user->email,
        ])
            ->assertSessionDoesntHaveErrors();
    }

    /** @test */
    public function testAUserWithANullPasswordSeesTheMigrationComponent()
    {
        $this->post(action([LoginController::class, 'login']), [
            'email' => $this->user->email,
        ])
            ->assertSeeLivewire('migrate');
    }

    /** @test */
    public function testTokenIsSentToUser()
    {
        $this->testStep1();

        Notification::assertSentTo($this->user, VerificationToken::class);
    }

    /** @test */
    public function testUserFirstNeedsToVerifyEmailAndIdentityThroughToken()
    {
        $this->testStep1()
            ->assertSee('Saisis le code à 6 chiffres');
    }

    /** @test */
    public function testTokenIsRequired()
    {
        $this->testStep1()
            ->set('token', null)
            ->call('verify')
            ->assertHasErrors('token');
    }

    /** @test */
    public function testTokenMustBeAString()
    {
        $this->testStep1()
            ->set('token', 123456)
            ->call('verify')
            ->assertHasErrors('token');
    }

    /** @test */
    public function testTokenMustBeExactly6Digits()
    {
        $this->testStep1()
            ->set('token', '123')
            ->call('verify')
            ->assertHasErrors('token');

        $this->testStep1()
            ->set('token', 'abc123')
            ->call('verify')
            ->assertHasErrors('token');
    }

    /** @test */
    public function testTokenMustMatchExpectedToken()
    {
        $testLivewireComponent = $this->testStep1();

        // Ensure we use an incorrect token.
        do {
            $incorrectToken = sprintf("%06d", mt_rand(0, 999999));
        } while ($incorrectToken === $this->getExpectedToken());

        $testLivewireComponent
            ->set('token', $incorrectToken)
            ->call('verify')
            ->assertHasErrors('token');
    }

    /** @test */
    public function testUserIsVerifiedWhenProvidingCorrectToken()
    {
        $this->testStep1()
            ->set('token', $this->getExpectedToken())
            ->call('verify')
            ->assertHasNoErrors()
            ->assertSet('verified', true);
    }

    /** @test */
    public function testCanRequestForTokenToBeResent()
    {
        $testLivewireComponent = $this->testStep1();

        Notification::assertSentTo($this->user, VerificationToken::class);

        $testLivewireComponent
            ->assertSee('Tu n’as pas reçu de code ?')
            ->call('resendToken')
            ->assertSet('resent', true)
            ->assertDontSee('Tu n’as pas reçu de code ?')
            ->assertSee('Le code a été renvoyé à ton adresse email');

        Notification::assertSentToTimes($this->user, VerificationToken::class, 2);
    }

    /** @test */
    public function testCanRequestForTokenToBeResentOnlyOnce()
    {
        $testLivewireComponent = $this->testStep1()
            ->call('resendToken')
            ->assertSet('resent', true);

        Notification::assertSentToTimes($this->user, VerificationToken::class, 2);

        Notification::fake(); // Reset counter

        $testLivewireComponent
            ->call('resendToken');

        Notification::assertNothingSent();
    }

    /** @test */
    public function testUserGetsPopulatedOnlyOnSecondStep()
    {
        $this->testStep1()
            ->assertSet('user', null);

        $this->assertTrue($this->testStep2()->get('user')->is($this->user));
    }

    /** @test */
    public function testClassBirthdateAndPhoneGetAssignedOnSecondStep()
    {
        $this->testStep2()
            ->assertSet('class_course', $this->user->class_course)
            ->assertSet('class_year', $this->user->class_year)
            ->assertSet('birthdate', $this->user->birthdate->toDateString())
            ->assertSet('phone', $this->user->phone->formatInternational());

        $this->user->update([
            'class_course' => null,
            'class_year' => null,
            'birthdate' => null,
            'phone' => null,
        ]);

        $this->testStep2()
            ->assertSet('class_course', '')
            ->assertSet('class_year', null)
            ->assertSet('birthdate', null)
            ->assertSet('phone', null);
    }

    /** @test */
    public function testMustSetClassIfClassCourseOrYearNull()
    {
        $this->testStep2()
            ->assertSet('mustSetClass', false);

        $this->user->update(['class_course' => 'EPL/S', 'class_year' => null]);

        $this->testStep2()
            ->assertSet('mustSetClass', true);

        $this->user->update(['class_course' => null, 'class_year' => 2015]);

        $this->testStep2()
            ->assertSet('mustSetClass', true);

        $this->user->update(['class_course' => null, 'class_year' => null]);

        $this->testStep2()
            ->assertSet('mustSetClass', true);
    }

    /** @test */
    public function testIfClassMustBeSetThenClassCourseIsRequired()
    {
        $this->user->update(['class_course' => null, 'class_year' => null]);

        $this->testStep2()
            ->set('class_course', null)
            ->call('saveUser')
            ->assertHasErrors(['class_course' => 'required']);
    }

    /** @test */
    public function testIfClassMustBeSetThenClassCourseMustBeRegisteredInConfig()
    {
        $this->user->update(['class_course' => null, 'class_year' => null]);

        $this->testStep2()
            ->set('class_course', 'Foobar')
            ->call('saveUser')
            ->assertHasErrors(['class_course' => 'in']);
    }

    /** @test */
    public function testIfClassMustBeSetThenClassYearIsRequired()
    {
        $this->user->update(['class_course' => null, 'class_year' => null]);

        $this->testStep2()
            ->set('class_year', null)
            ->call('saveUser')
            ->assertHasErrors(['class_year' => 'required']);
    }

    /** @test */
    public function testIfClassMustBeSetThenClassYearMustBeAFourDigitYear()
    {
        $this->user->update(['class_course' => null, 'class_year' => null]);

        $this->testStep2()
            ->set('class_year', 'Not a year')
            ->call('saveUser')
            ->assertHasErrors(['class_year' => 'digits']);

        $this->testStep2()
            ->set('class_year', '15')
            ->call('saveUser')
            ->assertHasErrors(['class_year' => 'digits']);
    }

    /** @test */
    public function testPasswordIsRequired()
    {
        $this->testStep2()
            ->set('password', null)
            ->call('saveUser')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function testPasswordMustBeLongerThan8Characters()
    {
        $this->testStep2()
            ->set('password', 'foobar')
            ->call('saveUser')
            ->assertHasErrors(['password' => 'min']);
    }

    /** @test */
    public function testPasswordMustBeConfirmed()
    {
        $this->testStep2()
            ->set('password', 'foobarbaz')
            ->set('password_confirmation', 'oopswrong')
            ->call('saveUser')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    /** @test */
    public function testBirthdateIsSetFromParts()
    {
        $this->testStep2()
            ->set('birthdate_day', 22)
            ->set('birthdate_month', 9)
            ->set('birthdate_year', 1994)
            ->call('saveUser')
            ->assertSet('birthdate', '1994-09-22');
    }

    /** @test */
    public function testBirthdateIsRequired()
    {
        $this->testStep2()
            ->set('birthdate_year', null)
            ->set('birthdate_month', null)
            ->set('birthdate_day', null)
            ->call('saveUser')
            ->assertHasErrors(['birthdate']);
    }

    /** @test */
    public function testBirthdateMustBeAValidDate()
    {
        $this->testStep2()
            ->set('birthdate_year', '1990')
            ->set('birthdate_month', '02')
            ->set('birthdate_day', '31')
            ->call('saveUser')
            ->assertHasErrors('birthdate');
    }

    /** @test */
    public function testGenderIsRequired()
    {
        $this->testStep2()
            ->set('gender', '')
            ->call('saveUser')
            ->assertHasErrors(['gender' => 'required']);
    }

    /** @test */
    public function testGenderMustBeRegisteredInConfig()
    {
        $this->testStep2()
            ->set('gender', 'Z')
            ->call('saveUser')
            ->assertHasErrors(['gender' => 'in']);
    }

    /** @test */
    public function testPhoneIsRequired()
    {
        $this->testStep2()
            ->set('phone', null)
            ->call('saveUser')
            ->assertHasErrors(['phone' => 'required']);
    }

    /** @test */
    public function testPhoneMustBeAValidNumber()
    {
        $this->testStep2()
            ->set('phone', 'n0t_4_ph0n3_numb3r')
            ->call('saveUser')
            ->assertHasErrors(['phone' => 'phone']);
    }

    /** @test */
    public function testUserInfoIsUpdated()
    {
        $this->user->update(['class_course' => null, 'class_year' => null]);

        $this->submitUserInfo()
            ->assertHasNoErrors();

        $this->user->refresh();

        $this->assertEquals('EPL/S', $this->user->class_course);
        $this->assertEquals(2015, $this->user->class_year);
        $this->assertTrue(Hash::check('password', $this->user->password));
        $this->assertEquals('1994-09-22', $this->user->birthdate->toDateString());
        $this->assertEquals('M', $this->user->gender);
        $this->assertEquals('+33 6 52 52 41 22', $this->user->phone->formatInternational());
    }

    /** @test */
    public function testOnSuccessfulSubmitUserIsAuthenticatedAndRedirected()
    {
        $this->submitUserInfo()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function testOnSuccessfulSubmitUserEmailIsMarkedVerified()
    {
        $this->submitUserInfo();

        $this->assertTrue($this->user->hasVerifiedEmail());
    }

    /**
     * @return \Livewire\Testing\TestableLivewire
     */
    protected function testStep1(): \Livewire\Testing\TestableLivewire
    {
        Notification::fake();

        $this->post(action([LoginController::class, 'login']), [
            'email' => $this->user->email,
        ]);

        Notification::fake(); // Reset the notification fake counter.

        return Livewire::test(Migrate::class);
    }

    /**
     * @return \Livewire\Testing\Concerns\MakesCallsToComponent|\Livewire\Testing\TestableLivewire
     */
    protected function testStep2()
    {
        return $this->testStep1()
            ->set('token', $this->getExpectedToken())
            ->call('verify');
    }

    /**
     * @return string
     */
    protected function getExpectedToken(): string
    {
        return Session::get('expectedToken');
    }

    /**
     * @return \Livewire\Testing\Concerns\MakesCallsToComponent|\Livewire\Testing\TestableLivewire
     */
    protected function submitUserInfo()
    {
        return $this->testStep2()
            ->set('class_course', 'EPL/S')
            ->set('class_year', 2015)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('birthdate_year', 1994)
            ->set('birthdate_month', 9)
            ->set('birthdate_day', 22)
            ->set('gender', 'M')
            ->set('phone', '06 52 52 41 22')
            ->call('saveUser');
    }
}
