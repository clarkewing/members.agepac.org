<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Location;
use App\User;
use Faker\Generator as Faker;
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

$factory->define(User::class, function (Faker $faker) {
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;

    return [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'username' => User::makeUsername($firstName, $lastName),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'class_course' => Arr::random(config('council.courses')),
        'class_year' => $faker->year,
        'gender' => Arr::random(array_keys(config('council.genders'))),
        'birthdate' => $faker->date('Y-m-d', today()->subYears(18)), // At least 18 years old
        'phone' => Arr::random([ // Use predefined numbers for testing as Faker can generate some weirdos
            '0669696969',
            '07 68 12 34 56',
            '06.12.34.56.78',
            '+44 7375 123456',
            '+1-202-555-5555',
        ]),
        'bio' => $faker->paragraph,
        'flight_hours' => $faker->numberBetween(0, 15000),
        'remember_token' => Str::random(10),
    ];
});

$factory->state(User::class, 'unverified_email', function (Faker $faker) {
    return [
        'email_verified_at' => null,
    ];
});

$factory->state(User::class, 'administrator', function (Faker $faker) {
    return [
        'email' => Arr::random(config('council.administrators')),
    ];
});
