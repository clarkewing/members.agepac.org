<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Location;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(Location::class, function (Faker $faker) {
    return [
        'locatable_id' => function () {
            return factory(User::class)->create()->id;
        },
        'locatable_type' => 'App\User',
        'type' => Arr::random(['country', 'city', 'address', 'busStop', 'trainStation', 'townhall', 'airport']),
        'name' => $faker->sentence,
        'street_line_1' => $faker->streetAddress,
        'street_line_2' => $faker->secondaryAddress,
        'municipality' => $faker->city,
        'administrative_area' => $faker->region,
        'sub_administrative_area' => null,
        'postal_code' => $faker->postcode,
        'country' => $country = $faker->country,
        'country_code' => function () {
            \Locale::setDefault(config('app.locale'));

            return array_rand(\Symfony\Component\Intl\Countries::getNames());
        },
    ];
});
