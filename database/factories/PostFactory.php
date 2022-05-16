<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'thread_id' => function () {
                return Thread::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'body' => $this->faker->paragraph(),
            'is_thread_initiator' => false,
        ];
    }

    public function withAttachment(bool $shouldAttach = true)
    {
        if (! $shouldAttach) {
            return $this;
        }

        return $this->state(function () {
            $attachment = Attachment::factory()->create();

            $trixAttachment = '<figure data-trix-attachment="'
                      . htmlentities(json_encode([
                          'contentType' => Storage::disk('public')->mimeType($attachment->path),
                          'filename' => basename($attachment->path),
                          'filesize' => Storage::disk('public')->size($attachment->path),
                          'id' => $attachment->id,
                          'href' => '/storage/' . $attachment->path,
                          'url' => '/storage/' . $attachment->path,
                      ]))
                      . '" class="attachment attachment--file"></figure>';

            return [
                'body' => $this->faker->paragraph() . $trixAttachment . $this->faker->paragraph(),
            ];
        });
    }

    public function fromExistingUser()
    {
        return $this->state(function () {
            return [
                'user_id' => function () {
                    return User::all()->random()->id;
                },
            ];
        });
    }
}
