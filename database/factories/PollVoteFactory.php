<?php

namespace Database\Factories;

use App\PollOption;
use App\PollVote;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollVoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PollVote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'option_id' => function () {
                return PollOption::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
