<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use App\Thread;
use App\User;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'thread_id' => function () {
            return factory(Thread::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'body' => $faker->paragraph,
        'is_thread_initiator' => false,
    ];
});
