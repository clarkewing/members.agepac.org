<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'type_code' => $faker->randomKey(Company::typeStrings()),
        'website' => $faker->url,
        'description' => $faker->paragraph,
        'operations' => $faker->paragraph,
        'conditions' => $faker->paragraph,
        'remarks' => $faker->paragraph,
    ];
});
