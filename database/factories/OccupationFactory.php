<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Aircraft;
use App\Company;
use App\Location;
use App\Occupation;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(Occupation::class, function (Faker $faker) {
    $is_pilot = $faker->boolean;

    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'position' => $is_pilot ? Arr::random(['CDB', 'OPL']) : $faker->jobTitle,
        'aircraft_id' => $is_pilot ? Aircraft::all()->random()->id : null,
        'company_id' => function () {
            return factory(Company::class)->create()->id;
        },
        'status' => Arr::random(array_keys(Occupation::statusStrings())),
        'description' => $faker->paragraph,
        'start_date' => $start_date = $faker->date,
        'end_date' => $faker->boolean ? $faker->dateTimeBetween($start_date, 'now') : null,
        'is_primary' => false,
    ];
});

$factory->state(Occupation::class, 'pilot', [
    'position' => Arr::random(['CDB', 'OPL']),
    'aircraft_id' => Aircraft::all()->random()->id,
]);

$factory->state(Occupation::class, 'not_pilot', function ($faker) {
    return [
        'position' => $faker->jobTitle,
        'aircraft_id' => null,
    ];
});

$factory->state(Occupation::class, 'past', function ($faker) {
    return [
        'start_date' => $start_date = $faker->date,
        'end_date' => $faker->dateTimeBetween($start_date, 'now'),
    ];
});

$factory->state(Occupation::class, 'current', function () {
    return [
        'end_date' => null,
    ];
});

$factory->afterCreating(Occupation::class, function ($occupation) {
    factory(Location::class)->create([
        'locatable_id' => $occupation->id,
        'locatable_type' => get_class($occupation),
    ]);
});
