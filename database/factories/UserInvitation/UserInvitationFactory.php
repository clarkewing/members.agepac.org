<?php



namespace Database\Factories\UserInvitation;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\UserInvitation;
use Illuminate\Support\Arr;

class UserInvitationsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserInvitation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'class_course' => Arr::random(config('council.courses')),
            'class_year' => $this->faker->year,
        ];
    }
}
