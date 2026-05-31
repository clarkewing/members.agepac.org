<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Livewire\ForcedPasswordReset;
use App\Models\User;
use App\Notifications\VerificationToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

class ForcedPasswordResetTest extends TestCase
{
    /** @var \App\Models\User */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => User::PASSWORD_RESET_SENTINEL,
        ]);
    }

    /** @test */
    public function testHashCheckAlwaysFailsAgainstTheSentinel()
    {
        $this->assertFalse(Hash::check('anything', User::PASSWORD_RESET_SENTINEL));
        $this->assertFalse(Hash::check('', User::PASSWORD_RESET_SENTINEL));
        $this->assertFalse(Hash::check(User::PASSWORD_RESET_SENTINEL, User::PASSWORD_RESET_SENTINEL));
    }

    /** @test */
    public function testInvalidatedUserSeesForcedResetComponentOnLoginAttempt()
    {
        $this->post(action([LoginController::class, 'login']), [
            'email' => $this->user->email,
            'password' => 'whatever-they-used-to-have',
        ])
            ->assertOk()
            ->assertViewIs('auth.reset-required')
            ->assertViewHas('user', fn ($u) => $u->is($this->user))
            ->assertSeeLivewire('forced-password-reset');
    }

    /** @test */
    public function testUnknownUserGetsStandardInvalidCredentialsPath()
    {
        // No special branch for unknown emails — they hit the normal failure
        // path, so the incident doesn't leak which accounts exist.
        Notification::fake();

        $this->post(action([LoginController::class, 'login']), [
            'email' => 'nobody@example.com',
            'password' => 'anything',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }

    /** @test */
    public function testLegacyNullPasswordUserStillHitsMigrateWizard()
    {
        $legacyUser = User::factory()->create([
            'email' => 'legacy@example.com',
            'password' => null,
        ]);

        $this->post(action([LoginController::class, 'login']), [
            'email' => $legacyUser->email,
        ])
            ->assertSeeLivewire('migrate');
    }

    /** @test */
    public function testMountSendsVerificationTokenToUser()
    {
        Notification::fake();

        $this->getComponent();

        Notification::assertSentTo($this->user, VerificationToken::class);
    }

    /** @test */
    public function testTokenIsRequired()
    {
        $this->getComponent()
            ->set('token', null)
            ->call('verify')
            ->assertHasErrors('token');
    }

    /** @test */
    public function testTokenMustBeExactlySixDigits()
    {
        $this->getComponent()
            ->set('token', '123')
            ->call('verify')
            ->assertHasErrors('token');

        $this->getComponent()
            ->set('token', 'abc123')
            ->call('verify')
            ->assertHasErrors('token');
    }

    /** @test */
    public function testTokenMustMatchExpectedToken()
    {
        $component = $this->getComponent();

        do {
            $incorrectToken = sprintf('%06d', mt_rand(0, 999999));
        } while ($incorrectToken === $this->getExpectedToken());

        $component
            ->set('token', $incorrectToken)
            ->call('verify')
            ->assertHasErrors('token');
    }

    /** @test */
    public function testCorrectTokenAdvancesToPasswordStep()
    {
        $this->getComponent()
            ->set('token', $this->getExpectedToken())
            ->call('verify')
            ->assertHasNoErrors()
            ->assertSet('verified', true);
    }

    /** @test */
    public function testCanRequestTokenResendOnce()
    {
        Notification::fake();

        $component = $this->getComponent();

        Notification::assertSentToTimes($this->user, VerificationToken::class, 1);

        $component
            ->call('resendToken')
            ->assertSet('resent', true);

        Notification::assertSentToTimes($this->user, VerificationToken::class, 2);

        Notification::fake(); // Reset counter

        $component->call('resendToken');

        Notification::assertNothingSent();
    }

    /** @test */
    public function testPasswordIsRequiredOnReset()
    {
        $this->verifiedComponent()
            ->set('password', null)
            ->call('resetPassword')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function testPasswordMustMeetDefaultsPolicy()
    {
        // Uses Rules\Password::defaults() — same rule object the standard
        // ResetPasswordController uses. Currently equivalent to min(8) but
        // tracks any project-wide policy changes via Password::defaults(...)
        // in a service provider.
        $this->verifiedComponent()
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('resetPassword')
            ->assertHasErrors('password');
    }

    /** @test */
    public function testPasswordMustBeConfirmed()
    {
        $this->verifiedComponent()
            ->set('password', 'NewSecurePassword123!')
            ->set('password_confirmation', 'mismatch')
            ->call('resetPassword')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    /** @test */
    public function testSuccessfulResetReplacesSentinelWithHashedPassword()
    {
        $this->verifiedComponent()
            ->set('password', 'NewSecurePassword123!')
            ->set('password_confirmation', 'NewSecurePassword123!')
            ->call('resetPassword');

        $this->user->refresh();

        $this->assertNotSame(User::PASSWORD_RESET_SENTINEL, $this->user->password);
        $this->assertTrue(Hash::check('NewSecurePassword123!', $this->user->password));
    }

    /** @test */
    public function testSuccessfulResetLogsTheUserInAndRedirectsHome()
    {
        $this->verifiedComponent()
            ->set('password', 'NewSecurePassword123!')
            ->set('password_confirmation', 'NewSecurePassword123!')
            ->call('resetPassword')
            ->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($this->user));
    }

    /** @test */
    public function testCannotResetPasswordWithoutVerifyingFirst()
    {
        // Server-side guard: even though the UI hides the password form
        // until verified, a Livewire client could call resetPassword
        // directly. The component must reject it.
        $this->getComponent()
            ->set('password', 'NewSecurePassword123!')
            ->set('password_confirmation', 'NewSecurePassword123!')
            ->call('resetPassword')
            ->assertForbidden();

        $this->user->refresh();

        $this->assertSame(User::PASSWORD_RESET_SENTINEL, $this->user->password);
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function testCannotBypassVerificationByTamperingWithVerifiedProperty()
    {
        // $verified is a public Livewire property, so a malicious client
        // can flip it to true in the request payload without going through
        // verify(). The updatedVerified() hook snaps it back to the
        // session-backed truth, and resetPassword()'s abort_unless is the
        // final boundary.
        $this->getComponent()
            ->set('verified', true)
            ->set('password', 'NewSecurePassword123!')
            ->set('password_confirmation', 'NewSecurePassword123!')
            ->call('resetPassword')
            ->assertForbidden();

        $this->user->refresh();

        $this->assertSame(User::PASSWORD_RESET_SENTINEL, $this->user->password);
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function testTamperingWithVerifiedDoesNotLeakUserFirstName()
    {
        // Without the updatedVerified() hook, flipping $verified to true
        // would advance the view to step 2 which renders "Bonjour
        // {{ $user->first_name }} !" — leaking the first name to anyone
        // who knows a sentinel user's email. The hook prevents this by
        // resetting $verified to the session value on every client update,
        // keeping the view on step 1.
        $this->user->update(['first_name' => 'Unguessable']);

        $this->getComponent()
            ->set('verified', true)
            ->assertSet('verified', false)
            ->assertDontSee('Unguessable')
            ->assertSee('Saisis le code à 6 chiffres');
    }

    /** @test */
    public function testLivewireBlocksRebindingTheUserProperty()
    {
        // Both users are sentinel users (true for everyone post-incident),
        // so an attacker who controls account A might try to swap $user to
        // account B between mount and resetPassword to ride A's verified
        // token. Livewire 2 blocks this at the framework level — it refuses
        // to rebind Eloquent model properties without an explicit rule,
        // throwing CannotBindToModelDataWithoutValidationRuleException.
        // Our authorizedUser() guard is defense-in-depth in case someone
        // later adds a rule for $user.
        $victim = User::factory()->create([
            'email' => 'victim@example.com',
            'password' => User::PASSWORD_RESET_SENTINEL,
        ]);

        $this->expectException(\Livewire\Exceptions\CannotBindToModelDataWithoutValidationRuleException::class);

        $this->getComponent()->set('user', $victim);
    }

    /**
     * @return \Livewire\Testing\TestableLivewire
     */
    protected function getComponent(): \Livewire\Testing\TestableLivewire
    {
        return Livewire::test(ForcedPasswordReset::class, ['user' => $this->user]);
    }

    /**
     * @return \Livewire\Testing\TestableLivewire
     */
    protected function verifiedComponent(): \Livewire\Testing\TestableLivewire
    {
        return $this->getComponent()
            ->set('token', $this->getExpectedToken())
            ->call('verify');
    }

    protected function getExpectedToken(): ?string
    {
        return Session::get('forcedPasswordReset.token');
    }
}
