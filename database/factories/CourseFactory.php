<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Course;
use App\Location;
use App\User;
use Faker\Generator as Faker;

$factory->define(Course::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'title' => $faker->jobTitle,
        'school' => $faker->company,
        'description' => $faker->paragraph,
        'start_date' => $start_date = $faker->date,
        'end_date' => $faker->boolean ? $faker->dateTimeBetween($start_date, 'now') : null,
    ];
});

$factory->afterCreating(Course::class, function ($course) {
    factory(Location::class)->create([
        'locatable_id' => $course->id,
        'locatable_type' => get_class($course),
    ]);
});
