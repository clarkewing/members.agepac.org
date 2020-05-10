<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserInvitation;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(UserInvitation::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'class_course' => Arr::random(config('council.courses')),
        'class_year' => $faker->year,
    ];
});
