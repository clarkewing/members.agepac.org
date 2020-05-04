<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Channel;
use Faker\Generator as Faker;

$factory->define(Channel::class, function (Faker $faker) {
    return [
        'parent' => $faker->boolean ? $faker->word : null,
        'name' => $faker->unique()->word,
        'description' => $faker->sentence,
        'archived' => false,
    ];
});
