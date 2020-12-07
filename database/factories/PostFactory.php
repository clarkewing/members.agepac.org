<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Attachment;
use App\Post;
use App\Thread;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;

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

$factory->state(Post::class, 'with_attachment', function (Faker $faker) {
    $attachment = factory(Attachment::class)->create();

    $trixAttachment = '<figure data-trix-attachment="'
                      .htmlentities(json_encode([
                          'contentType' => Storage::disk('public')->mimeType($attachment->path),
                          'filename' => basename($attachment->path),
                          'filesize' => Storage::disk('public')->size($attachment->path),
                          'id' => $attachment->id,
                          'href' => '/storage/'.$attachment->path,
                          'url' => '/storage/'.$attachment->path,
                      ]))
                      .'" class="attachment attachment--file"></figure>';

    return [
        'body' => $faker->paragraph.$trixAttachment.$faker->paragraph,
    ];
});

$factory->state(Post::class, 'from_existing_user', function (Faker $faker) {
    return [
        'user_id' => function () {
            return User::all()->random()->id;
        },
    ];
});
