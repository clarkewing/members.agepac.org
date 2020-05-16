<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Channel;
use App\Thread;
use App\User;
use Faker\Generator as Faker;

$factory->define(Thread::class, function (Faker $faker) {
    $title = $faker->sentence;

    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'channel_id' => function () {
            return factory(Channel::class)->create()->id;
        },
        'title' => $title,
        'locked' => false,
        'pinned' => false,
    ];
});

$factory->afterCreating(Thread::class, function ($thread, $faker) {
    $thread->addPost([
        'user_id' => $thread->creator->id,
        'body' => $faker->paragraph,
        'is_thread_initiator' => true,
    ]);
});

$factory->state(Thread::class, 'with_body', function (Faker $faker) {
    return [
        'body' => $faker->paragraph,
    ];
});

$factory->state(Thread::class, 'from_existing_channel_and_user', function (Faker $faker) {
    return [
        'user_id' => function () {
            return User::all()->random()->id;
        },
        'channel_id' => function () {
            return Channel::all()->random()->id;
        },
    ];
});
