<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Poll;
use App\Thread;
use Faker\Generator as Faker;

$factory->define(Poll::class, function (Faker $faker) {
    return [
        'thread_id' => function () {
            return factory(Thread::class)->create()->id;
        },
        'title' => 'Sondage',
        'votes_editable' => false,
        'max_votes' => 1,
        'votes_privacy' => 0,
        'results_before_voting' => false,
        'locked_at' => '2030-09-17T23:27:00+02:00',
    ];
});
