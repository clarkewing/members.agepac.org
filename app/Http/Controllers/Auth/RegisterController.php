<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route('home');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'class_course' => ['required', Rule::in(config('council.courses'))],
            'class_year' => ['required', 'digits:4'],
            'gender' => ['required', Rule::in(array_keys(config('council.genders')))],
            'birthdate' => ['required', 'date_format:Y-m-d', 'before:13 years ago'],
            'phone' => [
                'required',
                Rule::phone()->detect() // Auto-detect country if country code supplied
                    ->country('FR'), // Fallback to France if unable to auto-detect
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => User::makeUsername($data['first_name'], $data['last_name']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'class_course' => $data['class_course'],
            'class_year' => $data['class_year'],
            'gender' => $data['gender'],
            'birthdate' => $data['birthdate'],
            'phone' => $data['phone'],
        ]);
    }
}
