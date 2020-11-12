<?php

namespace App\Http\Livewire\Steps;

use App\User;
use Illuminate\Support\Facades\Hash;

trait Summary
{
    /**
     * Run the action.
     *
     * @return bool
     */
    public function runSummary(): bool
    {
        $this->createUser();

        $this->emit('success');

        return true;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \App\User
     */
    protected function createUser()
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

        $this->invitation->delete();

        return $user;
    }
}
