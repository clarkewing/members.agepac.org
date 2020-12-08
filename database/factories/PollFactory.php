<?php

namespace Database\Factories;

use App\Poll;
use App\PollOption;
use App\Thread;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poll::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function ($poll) {
            PollOption::factory()->count($this->faker->numberBetween(2, 10))->create([
                'poll_id' => $poll->id,
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
        return [
            'thread_id' => function () {
                return Thread::factory()->create()->id;
            },
            'title' => $this->faker->sentence,
            'votes_editable' => $this->faker->boolean,
            'max_votes' => 1,
            'votes_privacy' => $this->faker->randomElement(Poll::$votesPrivacyValues),
            'results_before_voting' => $this->faker->boolean,
            'locked_at' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
        ];
    }
}
