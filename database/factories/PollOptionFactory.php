<?php

namespace Database\Factories;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'poll_id' => function () {
                return Poll::factory()->create()->id;
            },
            'label' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
        ];
    }
}
