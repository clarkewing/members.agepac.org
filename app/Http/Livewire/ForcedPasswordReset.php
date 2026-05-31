<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Notifications\VerificationToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ForcedPasswordReset extends Component
{
    /**
     * Public Livewire properties are hydrated from the client payload on
     * every request, so they can be tampered with — never trust $verified
     * or $resent as authorization state.
     * The authoritative verified flag lives in the session under
     * SESSION_KEY and is re-checked on every server action.
     * $user is safe because Livewire 2 refuses to rebind Eloquent properties
     * without an explicit validation rule.
     */
    public User $user;

    public $token;

    public $verified = false;

    public $resent = false;

    public $password;

    public $password_confirmation;

    protected const SESSION_KEY = 'forcedPasswordReset';

    protected function rules()
    {
        return [
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function mount(User $user)
    {
        $this->user = $user;

        Session::put(self::SESSION_KEY, [
            'token' => null,
            'verified' => false,
        ]);

        $this->sendToken();
    }

    public function verify()
    {
        $this->validate([
            'token' => ['required', 'string', 'digits:6', Rule::in([$this->expectedToken()])],
        ]);

        Session::put(self::SESSION_KEY . '.verified', true);

        $this->verified = true;
    }

    /**
     * Snap the public $verified property back to the session-backed truth
     * on every client update, so a malicious client cannot flip it to true
     * to skip the email-token step or advance the view to step 2.
     */
    public function updatedVerified()
    {
        $this->verified = Session::get(self::SESSION_KEY . '.verified', false) === true;
    }

    public function resetPassword()
    {
        abort_unless(Session::get(self::SESSION_KEY . '.verified') === true, 403);

        $this->validate();

        $this->user->forceFill([
            'password' => Hash::make($this->password),
            'remember_token' => null,
        ])->save();

        Session::forget(self::SESSION_KEY);

        Auth::login($this->user);

        $this->redirect(route('home'));
    }

    public function sendToken(): void
    {
        $token = sprintf('%06d', mt_rand(0, 999999));

        Session::put(self::SESSION_KEY . '.token', $token);

        $this->user->notify(new VerificationToken($token));
    }

    public function resendToken(): void
    {
        if ($this->resent) {
            return;
        }

        $this->user->notify(new VerificationToken($this->expectedToken()));

        $this->resent = true;
    }

    protected function expectedToken(): ?string
    {
        return Session::get(self::SESSION_KEY . '.token');
    }
}
