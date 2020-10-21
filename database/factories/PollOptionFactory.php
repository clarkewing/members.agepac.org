<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Poll;
use App\PollOption;
use Faker\Generator as Faker;

$factory->define(PollOption::class, function (Faker $faker) {
    return [
        'poll_id' => function () {
            return factory(Poll::class)->create()->id;
        },
        'label' => $faker->sentence,
        'color' => $faker->hexColor,
    ];
});
