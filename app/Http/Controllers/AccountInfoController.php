<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountInfoController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('account.info', ['user' => Auth::user()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'not_present',
            'last_name' => 'not_present',
            'birthdate' => ['date_format:Y-m-d', 'before:13 years ago'],
            'phone' => [Rule::opinionatedPhone()],
            'email' => ['email', Rule::unique('users')->ignore($user->id)],
            'current_password' => [
                'password',
                Rule::requiredIf(function () use ($request, $user) {
                    return ($request->has('email') && $request->input('email') !== $user->email)
                        || $request->has('new_password');
                }),
            ],
            'new_password' => ['nullable', 'min:8', 'confirmed'],

        ]);

        $user = $this->updateUser($user, $request);

        if ($request->wantsJson()) {
            return $user;
        }

        return redirect()->route('account.edit')
            ->with('flash', 'Tes informations ont été mises à jour.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }

    /**
     * Update the user's data.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    protected function updateUser(Authenticatable $user, Request $request)
    {
        $user->fill($request->toArray());

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->input('new_password'));
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        return tap($user)->save();
    }
}
