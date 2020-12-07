<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => User::makeUsername($firstName, $lastName),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'class_course' => Arr::random(config('council.courses')),
            'class_year' => $this->faker->year,
            'gender' => Arr::random(array_keys(config('council.genders'))),
            'birthdate' => $this->faker->date('Y-m-d', today()->subYears(18)), // At least 18 years old
            'phone' => Arr::random([ // Use predefined numbers for testing as Faker can generate some weirdos
                '0669696969',
                '07 68 12 34 56',
                '06.12.34.56.78',
                '+44 7375 123456',
                '+1-202-555-5555',
            ]),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverifiedEmail()
    {
        return $this->state(function () {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
