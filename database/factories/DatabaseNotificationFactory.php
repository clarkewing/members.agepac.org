<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Notifications\DatabaseNotification;
use Ramsey\Uuid\Uuid;

$factory->define(DatabaseNotification::class, function (Faker $faker) {
    return [
        'id' => Uuid::uuid4()->toString(),
        'type' => \App\Notifications\ThreadWasUpdated::class,
        'notifiable_id' => function () {
            return Auth::id() ?? factory(User::class)->create()->id;
        },
        'notifiable_type' => \App\User::class,
        'data' => ['foo' => 'bar'],
    ];
});
