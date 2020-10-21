<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PollOption;
use App\PollVote;
use App\User;
use Faker\Generator as Faker;

$factory->define(PollVote::class, function (Faker $faker) {
    return [
        'option_id' => function () {
            return factory(PollOption::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
