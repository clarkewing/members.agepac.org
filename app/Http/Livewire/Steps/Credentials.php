<?php

namespace App\Http\Livewire\Steps;

trait Credentials
{
    public $email;

    public $password;
    public $password_confirmation;

    /**
     * Run the action.
     *
     * @return bool
     */
    public function runCredentials(): bool
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        return true;
    }
}
