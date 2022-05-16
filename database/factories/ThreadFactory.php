<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function ($thread) {
            $thread->addPost([
                'user_id' => $thread->creator->id,
                'body' => $this->faker->paragraph(),
                'is_thread_initiator' => true,
            ]);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence();

        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'channel_id' => function () {
                return Channel::factory()->create()->id;
            },
            'visits' => 0,
            'title' => $title,
            'locked' => false,
            'pinned' => false,
        ];
    }

    public function withBody()
    {
        return $this->state(function () {
            return [
                'body' => $this->faker->paragraph(),
            ];
        });
    }

    public function fromExistingChannelAndUser()
    {
        return $this->state(function () {
            return [
                'user_id' => function () {
                    return User::all()->random()->id;
                },
                'channel_id' => function () {
                    return Channel::all()->random()->id;
                },
                'visits' => $this->faker->numberBetween(0, 35),
            ];
        });
    }
}
