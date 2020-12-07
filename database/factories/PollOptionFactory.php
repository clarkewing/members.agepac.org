<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Poll;
use App\PollOption;

class PollOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PollOption::class;

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
            'label' => $this->faker->sentence,
            'color' => $this->faker->hexColor,
        ];
    }
}
