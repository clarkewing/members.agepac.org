<?php

namespace App\Http\Livewire\Steps;

use App\Models\UserInvitation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

trait Identity
{
    public $invitation;
    public $invitationNotFound = false;

    public $name;

    public $first_name;

    public $last_name;

    public $class_course = ''; // Ensure select is initially blank

    public $class_year;

    /**
     * Run the action.
     *
     * @return bool
     */
    public function runIdentity(): bool
    {
        return ! is_null($this->invitation);
    }

    /**
     * Search for a user invitation.
     *
     * @return void
     */
    public function findInvitation(): void
    {
        if ($this->shouldSearchByName()) {
            if (! $this->findUserInvitationByName()) {
                $this->setFirstAndLastFromName();

                return;
            }
        } else {
            if (! $this->findUserInvitationByDetails()) {
                $this->invitationNotFound = true;

                return;
            }
        }

        $this->setIdentityFromInvitation();
    }

    /**
     * Go back to search screen.
     *
     * @return void
     */
    public function searchAgain(): void
    {
        $this->invitation = null;
    }

    /**
     * Find a UserInvitation matching the supplied name.
     *
     * @return \App\Models\UserInvitation|null
     */
    protected function findUserInvitationByName(): ?UserInvitation
    {
        $this->validate([
            'name' => ['required', 'string'],
        ]);

        return $this->invitation = UserInvitation::where(
            Builder::concat('`first_name`', '" "', '`last_name`'), 'LIKE', $this->name
        )->first();
    }

    /**
     * Find a UserInvitation matching the supplied details.
     *
     * @return \App\Models\UserInvitation|null
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
        ])->first();
    }

    /**
     * Guess and fill user's first and last names from a name.
     *
     * @return void
     */
    protected function setFirstAndLastFromName(): void
    {
        $names = explode(' ', $this->name);

        $this->first_name = Str::title(Arr::pull($names, 0));
        $this->last_name = Str::title(implode(' ', $names));
    }

    /**
     * Fill identity fields from UserInvitation.
     *
     * @return void
     */
    protected function setIdentityFromInvitation(): void
    {
        $this->name = $this->invitation->first_name.' '.$this->invitation->last_name;
        $this->first_name = $this->invitation->first_name;
        $this->last_name = $this->invitation->last_name;
        $this->class_course = $this->invitation->class_course;
        $this->class_year = $this->invitation->class_year;
    }

    /**
     * Determine if we should search by name.
     *
     * @return bool
     */
    protected function shouldSearchByName(): bool
    {
        return is_null($this->first_name);
    }

    /**
     * Share variables with the view.
     *
     * @return void
     */
    protected function shareVarsIdentity(): void
    {
        View::share('shouldSearchByName', $this->shouldSearchByName());
    }
}
