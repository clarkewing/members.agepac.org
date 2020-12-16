<?php

namespace App\Http\Livewire;

use App\Notifications\VerificationToken;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Migrate extends Component
{
    public $token;

    public $verified = false;
    public $resent = false;

    public $user; // Set only once verified.

    public $class_course = ''; // Ensure select is initially blank
    public $class_year;
    public $password;
    public $password_confirmation;
    public $birthdate;
    public $birthdate_day = ''; // Ensure select is initially blank
    public $birthdate_month = ''; // Ensure select is initially blank
    public $birthdate_year = ''; // Ensure select is initially blank
    public $gender = ''; // Ensure select is initially blank
    public $phone;

    protected function rules()
    {
        return [
            'class_course' => ['required', Rule::in(config('council.courses'))],
            'class_year' => ['required', 'digits:4'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'gender' => ['required', Rule::in(array_keys(config('council.genders')))],
            'phone' => ['required', Rule::opinionatedPhone()],
        ];
    }

    public function mount()
    {
        $this->sendToken();
    }

    public function verify()
    {
        $this->validate([
            'token' => ['required', 'string', 'digits:6', Rule::in([$this->getExpectedToken()])],
        ]);

        $this->verified = true;

        $this->populateUser();
    }

    public function saveUser()
    {
        $this->setBirthdate();

        $this->validate();

        if ($this->mustSetClass) {
            $this->fillUserClass();
        }

        $this->fillUserInfo();

        $this->markUserEmailAsVerified();

        Auth::login(tap($this->user)->save());

        $this->redirect(route('home'));
    }

    public function sendToken(): void
    {
        $this->getUser()->notify(new VerificationToken($this->generateExpectedToken()));
    }

    public function resendToken(): void
    {
        if ($this->resent) {
            return;
        }

        $this->getUser()->notify(new VerificationToken($this->getExpectedToken()));

        $this->resent = true;
    }

    public function getMustSetClassProperty()
    {
        return is_null($this->user->class_course) || is_null($this->user->class_year);
    }

    public function updatedBirthdate()
    {
        if (is_null($this->birthdate)) {
            $this->birthdate_year = $this->birthdate_month = $this->birthdate_day = '';

            return;
        }

        try {
            $birthdate = Carbon::parse($this->birthdate);

            $this->birthdate_year = $birthdate->year;
            $this->birthdate_month = $birthdate->month;
            $this->birthdate_day = $birthdate->day;
        } catch (InvalidFormatException $e) {
            return;
        }
    }

    protected function generateExpectedToken(): string
    {
        return tap(sprintf('%06d', mt_rand(0, 999999)),
            function ($value) {
                Session::put('expectedToken', $value);
            }
        );
    }

    protected function getUser()
    {
        return Session::get('unmigratedUser');
    }

    protected function getExpectedToken()
    {
        return Session::get('expectedToken');
    }

    protected function setBirthdate(): void
    {
        try {
            $this->birthdate = Carbon::createFromDate(
                $this->birthdate_year,
                $this->birthdate_month,
                $this->birthdate_day,
                'UTC'
            )->toDateString();
        } catch (InvalidFormatException $e) {
            $this->birthdate = null;
        }

        $this->validate([
            'birthdate' => [
                'required',
                'date_format:Y-m-d',
                'before:13 years ago',
                Rule::in([
                    sprintf('%04d', $this->birthdate_year)
                    . '-' . sprintf('%02d', $this->birthdate_month)
                    . '-' . sprintf('%02d', $this->birthdate_day),
                ]),
            ],
        ]);
    }

    protected function populateUser(): void
    {
        $this->user = $this->getUser();

        $this->class_course = $this->user->class_course ?? '';

        $this->class_year = $this->user->class_year;

        $this->birthdate = optional($this->user->birthdate)->toDateString();
        $this->updatedBirthdate();

        $this->phone = optional($this->user->phone)->formatInternational();
    }

    protected function fillUserInfo(): void
    {
        $this->user->fill([
            'password' => Hash::make($this->password),
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'phone' => $this->phone,
        ]);
    }

    protected function fillUserClass(): void
    {
        $this->user->fill([
            'class_course' => $this->class_course,
            'class_year' => $this->class_year,
        ]);
    }

    protected function markUserEmailAsVerified()
    {
        $this->user->forceFill([
            'email_verified_at' => now(),
        ]);
    }
}
