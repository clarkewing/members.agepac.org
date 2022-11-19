<?php

namespace Tests\Feature;

use App\Http\Livewire\UpdateProfileInformationForm;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    public function test_current_profile_information_is_available()
    {
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->name, $component->state['name']);
        $this->assertEquals($user->email, $component->state['email']);
    }

    public function test_profile_information_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfileInformationForm::class)
                ->set('state', [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'gender' => 'M',
                    'birthdate' => '1990-01-01',
                    'email' => 'test@example.com',
                    'phone' => '+33 6 12 34 56 78',
                ])
                ->call('updateProfileInformation');

        $user->refresh();
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('M', $user->gender);
        $this->assertEquals('1990-01-01', $user->birthdate->toDateString());
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('+33 6 12 34 56 78', $user->phone->formatInternational());
    }
}
