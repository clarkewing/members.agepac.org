<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        login as protected traitLogin;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
        ]);

        return $this->handleUnmigratedUser()
            ?: $this->handleResetRequiredUser($request)
            ?: $this->traitLogin($request);
    }

    /**
     * Catch users whose password was force-invalidated after the security
     * incident. Hand them off to the ForcedPasswordReset Livewire flow so
     * they can verify by email token and set a new password without leaving
     * the site.
     *
     * @return \Illuminate\Contracts\View\View|bool
     */
    protected function handleResetRequiredUser(Request $request)
    {
        $user = User::where($this->username(), $request->input($this->username()))
            ->where('password', User::PASSWORD_RESET_SENTINEL)
            ->first();

        if (is_null($user)) {
            return false;
        }

        return view('auth.reset-required', ['user' => $user]);
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route('home');
    }

    /**
     * Determine is the user hasn't yet completed the transition wizard,
     * and if so, direct them to the migrate view.
     * Used for users migrated from legacy site.
     *
     * @return \Illuminate\Contracts\View\View|bool
     */
    protected function handleUnmigratedUser()
    {
         $unmigratedUser = User::where($this->username(), request()->input($this->username()))
            ->whereNull('password')
            ->first();

        if (! is_null($unmigratedUser)) {
            Session::put(compact('unmigratedUser'));

            return view('auth.migrate');
        }

        return false;
    }
}
