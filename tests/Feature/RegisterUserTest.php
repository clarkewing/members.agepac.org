<?php

namespace Tests\Feature;

use App\Http\Livewire\Register;
use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\UserPendingApproval;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        Notification::fake();
    }

    /** @test */
    public function testGuestCanSeeRegisterComponent()
    {
        $this->get(route('register'))
            ->assertSeeLivewire('register');
    }

    /** @test */
    public function testNameIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 0)
            ->set('name', '')
            ->call('run')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function testNameMustBeString()
    {
        Livewire::test(Register::class)
            ->set('active', 0)
            ->set('name', 42)
            ->call('run')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function testIfNameMatchesNoInvitationItAsksForMoreInfo()
    {
        Livewire::test(Register::class)
            ->set('active', 0)
            ->set('name', 'John Doe')
            ->call('run')
            ->assertSet('first_name', 'John')
            ->assertSet('last_name', 'Doe')
            ->assertSet('class_course', '')
            ->assertSet('class_year', null)
            ->assertSeeInOrder(['Prénom', 'Nom', 'Cursus', 'Promotion']);
    }

    /** @test */
    public function testIfNameMatchesNoInvitationItGuessesFirstAndLastName()
    {
        Livewire::test(Register::class)
            ->set('active', 0)
            ->set('name', 'John Doe')
            ->call('run')
            ->assertSet('first_name', 'John')
            ->assertSet('last_name', 'Doe');

        Livewire::test(Register::class)
            ->set('active', 0)
            ->set('name', 'John')
            ->call('run')
            ->assertSet('first_name', 'John')
            ->assertSet('last_name', null);
    }

    /** @test */
    public function testIfNameMatchesInvitationItAsksForConfirmation()
    {
        $invitation = UserInvitation::factory()->create();

        Livewire::test(Register::class)
            ->set('active', 0)
            ->set('name', "$invitation->first_name $invitation->last_name")
            ->call('run')
            ->assertSet('invitation.id', $invitation->id)
            ->assertSet('first_name', $invitation->first_name)
            ->assertSet('last_name', $invitation->last_name)
            ->assertSet('class_course', $invitation->class_course)
            ->assertSet('class_year', $invitation->class_year)
            ->assertSeeInOrder(['Prénom', 'Nom', 'Promotion'])
            ->assertSee('C’est bien moi', false);
    }

    /** @test */
    public function testIfFullDetailsMatchInvitationItAsksForConfirmation()
    {
        $invitation = UserInvitation::factory()->create();

        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', $invitation->first_name)
            ->set('last_name', $invitation->last_name)
            ->set('class_course', $invitation->class_course)
            ->set('class_year', $invitation->class_year)
            ->call('run')
            ->assertSet('invitation.id', $invitation->id)
            ->assertSet('first_name', $invitation->first_name)
            ->assertSet('last_name', $invitation->last_name)
            ->assertSet('class_course', $invitation->class_course)
            ->assertSet('class_year', $invitation->class_year)
            ->assertSeeInOrder(['Prénom', 'Nom', 'Promotion'])
            ->assertSee('C’est bien moi');
    }

    /** @test */
    public function testIfFullDetailsMatchNoInvitationItAllowsContinuingWithRegistration()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('class_course', 'EPL/S')
            ->set('class_year', 2015)
            ->call('run')
            ->assertSet('invitation', null)
            ->assertSet('active', '3');
    }

    /** @test */
    public function testFirstNameIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', '')
            ->call('run')
            ->assertHasErrors(['first_name']);
    }

    /** @test */
    public function testFirstNameCannotExceed255Characters()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', str_repeat('a', 256))
            ->call('run')
            ->assertHasErrors(['first_name']);
    }

    /** @test */
    public function testLastNameIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', 'foo') // Ensure findUserInvitationByDetails is called
            ->set('last_name', '')
            ->call('run')
            ->assertHasErrors(['last_name']);
    }

    /** @test */
    public function testLastNameCannotExceed255Characters()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', 'foo') // Ensure findUserInvitationByDetails is called
            ->set('last_name', str_repeat('a', 256))
            ->call('run')
            ->assertHasErrors(['last_name']);
    }

    /** @test */
    public function testClassCourseIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', 'foo') // Ensure findUserInvitationByDetails is called
            ->set('class_course', '')
            ->call('run')
            ->assertHasErrors(['class_course']);
    }

    /** @test */
    public function testClassCourseMustBeRegisteredInConfig()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', 'foo') // Ensure findUserInvitationByDetails is called
            ->set('class_course', 'Foobar')
            ->call('run')
            ->assertHasErrors(['class_course']);
    }

    /** @test */
    public function testClassYearIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', 'foo') // Ensure findUserInvitationByDetails is called
            ->set('class_year', '')
            ->call('run')
            ->assertHasErrors(['class_year']);
    }

    /** @test */
    public function testClassYearMustBeFourDigitYear()
    {
        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', 'foo') // Ensure findUserInvitationByDetails is called
            ->set('class_year', 'not-a-year')
            ->call('run')
            ->assertHasErrors(['class_year']);

        Livewire::test(Register::class)
            ->set('active', 1)
            ->set('first_name', 'foo') // Ensure findUserInvitationByDetails is called
            ->set('class_year', 12)
            ->call('run')
            ->assertHasErrors(['class_year']);
    }

    /** @test */
    public function testIfGuestConfirmsInvitationItAsksForCredentials()
    {
        $invitation = UserInvitation::factory()->create();

        Livewire::test(Register::class)
            ->set('active', 2)
            ->set('invitation', $invitation)
            ->set('first_name', $invitation->first_name)
            ->set('last_name', $invitation->last_name)
            ->set('class_course', $invitation->class_course)
            ->set('class_year', $invitation->class_year)
            ->call('run')
            ->assertHasNoErrors()
            ->assertSet('active', 3)
            ->assertSeeInOrder(['Adresse email', 'Mot de passe', 'Confirmation']);
    }

    /** @test */
    public function testEmailIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 3)
            ->set('email', '')
            ->call('run')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function testEmailCannotExceed255Characters()
    {
        Livewire::test(Register::class)
            ->set('active', 3)
            ->set('email', str_repeat('a', 256) . '@example.com')
            ->call('run')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function testEmailMustBeValid()
    {
        Livewire::test(Register::class)
            ->set('active', 3)
            ->set('email', 'not-an-email')
            ->call('run')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function testEmailMustBeUnique()
    {
        User::factory()->create(['email' => 'john@example.com']);

        Livewire::test(Register::class)
            ->set('active', 3)
            ->set('email', 'john@example.com')
            ->call('run')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function testPasswordIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 3)
            ->set('password', '')
            ->call('run')
            ->assertHasErrors(['password']);
    }

    /** @test */
    public function testPasswordMustBeLongerThan8Characters()
    {
        Livewire::test(Register::class)
            ->set('active', 3)
            ->set('password', 'foobar')
            ->call('run')
            ->assertHasErrors(['password']);
    }

    /** @test */
    public function testPasswordMustBeConfirmed()
    {
        Livewire::test(Register::class)
            ->set('active', 3)
            ->set('password', 'foobarbaz')
            ->set('password_confirmation', 'oopswrong')
            ->call('run')
            ->assertHasErrors(['password']);
    }

    /** @test */
    public function testAfterValidCredentialsItAsksForDetails()
    {
        Livewire::test(Register::class)
            ->set('active', 3)
            ->set('email', 'john@example.com')
            ->set('password', 'topsecret')
            ->set('password_confirmation', 'topsecret')
            ->call('run')
            ->assertHasNoErrors()
            ->assertSet('active', 4)
            ->assertSeeInOrder(['Date de naissance', 'Genre', 'Numéro de téléphone']);
    }

    /** @test */
    public function testGenderIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('gender', '')
            ->call('run')
            ->assertHasErrors(['gender']);
    }

    /** @test */
    public function testGenderMustBeRegisteredInConfig()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('gender', 'Z')
            ->call('run')
            ->assertHasErrors(['gender']);
    }

    /** @test */
    public function testBirthdateIsSetFromParts()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('birthdate_day', 22)
            ->set('birthdate_month', 9)
            ->set('birthdate_year', 1994)
            ->call('run')
            ->assertSet('birthdate', '1994-09-22');
    }

    /** @test */
    public function testBirthdateIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('birthdate', '')
            ->call('run')
            ->assertHasErrors(['birthdate' => 'required']);
    }

    /** @test */
    public function testBirthdateMustBeAValidDate()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('birthdate', 'not-a-date')
            ->call('run')
            ->assertHasErrors(['birthdate' => 'date_format']);

        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('birthdate', '1990-02-31')
            ->call('run')
            ->assertHasErrors(['birthdate' => 'date_format']);
    }

    /** @test */
    public function testBirthdateMustBeOfIso8601Format()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('birthdate', '22/09/1994')
            ->call('run')
            ->assertHasErrors(['birthdate' => 'date_format']);
    }

    /** @test */
    public function testUserMustBeOlderThanThirteen() // Plenty of margin there...
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('birthdate', now()->subYears(10)->toDateString())
            ->call('run')
            ->assertHasErrors(['birthdate' => 'before']);
    }

    /** @test */
    public function testPhoneIsRequired()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('phone', '')
            ->call('run')
            ->assertHasErrors(['phone']);
    }

    /** @test */
    public function testPhoneMustBeAValidNumber()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('phone', 'n0t_4_ph0n3_numb3r')
            ->call('run')
            ->assertHasErrors(['phone']);
    }

    /** @test */
    public function testAfterValidDetailsItShowsSummary()
    {
        Livewire::test(Register::class)
            ->set('active', 4)
            ->set('birthdate', '1994-09-22')
            ->set('gender', 'M')
            ->set('phone', '06 23 45 67 89')
            ->call('run')
            ->assertHasNoErrors()
            ->assertSet('active', 5)
            ->assertSee('Vérification')
            ->assertSeeInOrder(['Nom et promotion', 'Identifiants', 'Détails supplémentaires'])
            ->assertSee('Terminé !');
    }

    /** @test */
    public function testIfUserValidatesSummaryItShowsSuccess()
    {
        $this->fillForm()
            ->call('run')
            ->assertSet('active', 6)
            ->assertSee('Bienvenue à l’AGEPAC !');
    }

    /** @test */
    public function testIfUserValidatesSummaryItCreatesAnAccount()
    {
        $this->assertDatabaseCount('users', 0);

        $this->fillForm($user = User::factory()->make()->getAttributes())
            ->call('run');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
        ]);
        $this->assertTrue(Hash::check('password', User::first()->password));
    }

    /** @test */
    public function testWithoutInvitationUserNeedsToBeApproved()
    {
        $this->fillForm()->call('run');

        $this->assertFalse(Auth::user()->isApproved());
    }

    /** @test */
    public function testUnapprovedUserTriggersNotificationToUsersWithPermission()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('users.approve');

        $this->fillForm()->call('run');

        Notification::assertSentToTimes($admin, UserPendingApproval::class);
    }

    /** @test */
    public function testWithInvitationUserIsAutomaticallyApproved()
    {
        $userInvitation = UserInvitation::factory()->create();

        $this->fillForm(Arr::only(
            $userInvitation->toArray(),
            ['first_name', 'last_name', 'class_course', 'class_year']
        ))
            ->call('run');

        $this->assertTrue(Auth::user()->isApproved());
    }

    /** @test */
    public function testUserInvitationIsDeletedAfterRegistration()
    {
        $userInvitation = UserInvitation::factory()->create();
        $this->assertDatabaseHas('user_invitations', ['id' => $userInvitation->id]);

        $this->fillForm(Arr::only(
            $userInvitation->toArray(),
            ['first_name', 'last_name', 'class_course', 'class_year']
        ))
            ->call('run');

        $this->assertDatabaseMissing('user_invitations', ['id' => $userInvitation->id]);
    }

    /** @test */
    public function testUserLoggedInUponRegistration()
    {
        $this->fillForm()->call('run');

        $this->assertAuthenticated();
    }

    /** @test */
    public function testConfirmationEmailSentUponRegistration()
    {
        $this->fillForm()->call('run');

        Notification::assertSentTo(
            [User::latest()->first()], VerifyEmail::class
        );
    }

    /** @test */
    public function testNewUserCanVerifyTheirEmail()
    {
        $this->fillForm()->call('run');

        $user = Auth::user();
        $this->flushSession();

        $this->assertNull($user->email_verified_at);

        $this->get((new VerifyEmail)->toMail($user)->actionUrl)
            ->assertRedirect(route('home'));

        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function testPhoneCountryCanBeAutoDetectedFromCountryCode()
    {
        $this->fillForm(['phone' => '+1 (202) 456-1414'])->call('run'); // White House

        $this->assertEquals('US', Auth::user()->phone->getCountry());
    }

    /**
     * TODO: Find a way to force the server variable change.
     * @test
     * @group external-api
     * @group geoip-api
     */
//    public function testPhoneWithoutCountryCodeFallsbackToFranceThenGeoIpBased()
//    {
//        // US IP address (using Google data)
//        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8']);
//
//        $this->fillForm(['phone' => '05 62 17 40 00'])->call('run'); // ENAC Toulouse number
//
//        $this->assertEquals('FR', Auth::user()->phone->getCountry());
//
//        Auth::logout();
//
//        $this->fillForm(['phone' => '(650) 253-0000'])->call('run'); // Google California number
//
//        $this->assertEquals('US', Auth::user()->phone->getCountry());
//    }

    /**
     * Fills in form to register.
     *
     * @param  array  $overrides
     * @return \Livewire\Testing\TestableLivewire
     */
    public function fillForm(array $overrides = [])
    {
        $data = array_merge(
            User::factory()->raw(
                Arr::only($overrides, ['first_name', 'last_name', 'class_course', 'class_year'])
            ),
            $overrides,
            ['password' => 'password']
        );

        $invitationExists = UserInvitation::where([
            ['first_name', $data['first_name']],
            ['last_name', $data['last_name']],
            ['class_course', $data['class_course']],
            ['class_year', $data['class_year']],
        ])->exists();

        $test = Livewire::test(Register::class)
            ->assertSet('active', 0) // Name
            ->set('name', $data['first_name'] . ' ' . $data['last_name'])
            ->call('run');

        if ($invitationExists) {
            $test = $test->assertSet('active', 2) // Invitation Confirmation
                ->call('run');
        } else {
            $test = $test->assertSet('active', 1) // Invitation Confirmation
                ->set('first_name', $data['first_name'])
                ->set('last_name', $data['last_name'])
                ->set('class_course', $data['class_course'])
                ->set('class_year', $data['class_year'])
                ->call('run');
        }

        return $test->assertSet('active', 3) // Credentials
            ->set('email', $data['email'])
            ->set('password', $data['password'])
            ->set('password_confirmation', $data['password'])
            ->call('run')
            ->assertSet('active', 4) // Details
            ->set('birthdate_day', substr($data['birthdate'], 8, 2))
            ->set('birthdate_month', substr($data['birthdate'], 5, 2))
            ->set('birthdate_year', substr($data['birthdate'], 0, 4))
            ->set('gender', $data['gender'])
            ->set('phone', is_object($data['phone']) ? $data['phone']->formatInternational() : $data['phone'])
            ->call('run')
            ->assertSet('active', 5);
    }
}
