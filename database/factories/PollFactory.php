<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Poll;
use App\PollOption;
use App\Thread;
use Faker\Generator as Faker;

$factory->define(Poll::class, function (Faker $faker) {
    return [
        'thread_id' => function () {
            return factory(Thread::class)->create()->id;
        },
        'title' => $faker->sentence,
        'votes_editable' => $faker->boolean,
        'max_votes' => 1,
        'votes_privacy' => $faker->numberBetween(0, 2),
        'results_before_voting' => $faker->boolean,
        'locked_at' => $faker->dateTimeBetween('now', '+1 year'),
    ];
});

$factory->afterCreating(Poll::class, function ($poll, Faker $faker) {
    factory(PollOption::class, $faker->numberBetween(1, 10))->create([
        'poll_id' => $poll->id,
    ]);
});

$factory->afterMaking(Poll::class, function ($poll, Faker $faker) {
    $poll->options = factory(PollOption::class, $faker->numberBetween(1, 10))->make([
        'poll_id' => null,
    ])->toArray();
});
