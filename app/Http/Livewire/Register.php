<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;
use Torann\GeoIP\Facades\GeoIP;

class Register extends MultiStepForm
{
    /**
     * List of the steps.
     */
    public array $steps = [
        'Name',
        'Identity',
        'Invitation Confirmation',
        'Credentials',
        'Details',
        'Summary',
        'Success',
    ];

    public function renderView(): \Illuminate\Contracts\View\View
    {
        return view('livewire.register');
    }

    public $invitation;

    public $name;

    public $first_name;

    public $last_name;

    public $class_course = ''; // Ensure select is initially blank

    public $class_year;

    public $email;

    public $password;

    public $password_confirmation;

    public $birthdate;

    public $birthdate_day = ''; // Ensure select is initially blank

    public $birthdate_month = ''; // Ensure select is initially blank

    public $birthdate_year = ''; // Ensure select is initially blank

    public $gender = ''; // Ensure select is initially blank

    public $phone;

    public function runName(): void
    {
        try {
            $this->setIdentityFromInvitation(
                $this->findUserInvitationByName()
            );

            $this->goToStep('Invitation Confirmation');
        } catch (ModelNotFoundException | MultipleRecordsFoundException) {
            $this->guessNames();

            $this->goToStep('Identity');
        }
    }

    public function runIdentity(): void
    {
        try {
            $this->setIdentityFromInvitation(
                $this->findUserInvitationByDetails()
            );

            $this->goToStep('Invitation Confirmation');
        } catch (ModelNotFoundException | MultipleRecordsFoundException) {
            $this->goToStep('Credentials');
        }
    }

    public function runInvitationConfirmation(): void
    {
    }

    public function runCredentials(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function runDetails(): void
    {
        $this->setBirthdate();

        $this->validate([
            'gender' => ['required', Rule::in(array_keys(config('council.genders')))],
            'birthdate' => ['required', 'date_format:Y-m-d', 'before:13 years ago'],
            'phone' => ['required', Rule::opinionatedPhone()],
        ]);

        $this->formatPhone();
    }

    public function runSummary(): void
    {
        $this->createUser();

        $this->emit('success');
    }

    public function resetIdentity(): void
    {
        unset($this->invitation);
        $this->class_course = '';
        $this->class_year = '';

        $this->guessNames();

        $this->goToStep('Identity');
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function createUser(): User
    {
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => User::makeUsername($this->first_name, $this->last_name),
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'class_course' => $this->class_course,
            'class_year' => $this->class_year,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'phone' => $this->phone,
        ]);

        if (! is_null($this->invitation)) {
            $this->invitation->delete();

            $user->markAsApproved();
        }

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }

    /**
     * Find UserInvitations matching the supplied name.
     */
    protected function findUserInvitationByName(): UserInvitation
    {
        $this->validate([
            'name' => ['required', 'string'],
        ]);

        return $this->invitation = UserInvitation::where(
            Builder::concat('`first_name`', '" "', '`last_name`'),
            'LIKE',
            $this->name
        )->sole();
    }

    /**
     * Find a UserInvitation matching the supplied details.
     */
    protected function findUserInvitationByDetails(): ?UserInvitation
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'class_course' => ['required', Rule::in(config('council.courses'))],
            'class_year' => ['required', 'digits:4'],
        ]);

        return $this->invitation = UserInvitation::where([
            ['first_name', 'LIKE', $this->first_name],
            ['last_name', 'LIKE', $this->last_name],
            ['class_course', '=', $this->class_course],
            ['class_year', '=', $this->class_year],
        ])->sole();
    }

    /**
     * Guess and fill user's first and last names from a name.
     */
    protected function guessNames(): void
    {
        $names = explode(' ', $this->name);

        $this->first_name = Str::title(Arr::pull($names, 0));
        $this->last_name = Str::title(implode(' ', $names));
    }

    /**
     * Fill identity fields from UserInvitation.
     */
    protected function setIdentityFromInvitation(UserInvitation $invitation): void
    {
        $this->name = $invitation->first_name . ' ' . $invitation->last_name;
        $this->first_name = $invitation->first_name;
        $this->last_name = $invitation->last_name;
        $this->class_course = $invitation->class_course;
        $this->class_year = $invitation->class_year;

        $this->invitation = $invitation;
    }

    /**
     * Set birthdate from parts.
     *
     * @return void
     */
    protected function setBirthdate(): void
    {
        if (empty($this->birthdate_day)
            || empty($this->birthdate_month)
            || empty($this->birthdate_year)
        ) {
            return;
        }

        $birthdate = Carbon::createFromDate(
            $this->birthdate_year,
            $this->birthdate_month,
            $this->birthdate_day,
            'UTC'
        );

        $this->birthdate_year = $birthdate->year;
        $this->birthdate_month = $birthdate->month;
        $this->birthdate_day = $birthdate->day;

        $this->birthdate = $birthdate->toDateString();
    }

    /**
     * Set phone to standardized format.
     *
     * @return void
     */
    protected function formatPhone(): void
    {
        $this->phone = PhoneNumber::make($this->phone)
            ->ofCountry('AUTO')
            ->ofCountry('FR')
            ->ofCountry(GeoIP::getLocation(request()->ip())->iso_code)
            ->formatInternational();
    }
}
