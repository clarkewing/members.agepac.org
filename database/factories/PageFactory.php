<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Page;
use Faker\Generator as Faker;

$factory->define(Page::class, function (Faker $faker) {
    $faker->addProvider(new \App\FakerProviders\Gutenberg($faker));

    return [
        'title' => $faker->sentence,
        'path' => $faker->unique()->parse($faker->randomElement(['{{slug}}/{{slug}}', '{{slug}}'])),
        'body' => $faker->gutenberg,
        'restricted' => false,
        'published_at' => now()->toDateTimeString(),
    ];
});
