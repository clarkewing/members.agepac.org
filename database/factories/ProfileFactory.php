<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Location;
use App\Profile;
use App\User;
use Faker\Generator as Faker;

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

$factory->define(Profile::class, function (Faker $faker) {
    return factory(User::class)->raw([
        'bio' => $faker->paragraph,
        'flight_hours' => $faker->numberBetween(0, 15000),
    ]);
});

$factory->afterCreating(Profile::class, function ($profile) {
    $profile->location()->save(factory(Location::class)->make());
});
