<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Attachment;
use Faker\Generator as Faker;
use function GuzzleHttp\Psr7\mimetype_from_filename;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

$factory->define(Attachment::class, function (Faker $faker) {
    $file = $faker->boolean
        ? UploadedFile::fake()->image($fileName = "{$faker->word}.jpg")
        : UploadedFile::fake()->create(
            $fileName = "{$faker->word}.{$faker->fileExtension}",
            $faker->numberBetween(20, 10000),
            mimetype_from_filename($fileName)
        );

    return [
        'post_id' => null,
        'path' => Storage::disk('public')->putFileAs(
            'attachments/'.Str::random(40),
            $file,
            $fileName
        ),
    ];
});
