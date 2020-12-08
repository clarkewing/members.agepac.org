<?php

namespace Database\Factories;

use App\Course;
use App\Location;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function ($course) {
            Location::factory()->create([
                'locatable_id' => $course->id,
                'locatable_type' => get_class($course),
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
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'title' => $this->faker->jobTitle,
            'school' => $this->faker->company,
            'description' => $this->faker->paragraph,
            'start_date' => $start_date = $this->faker->date,
            'end_date' => $this->faker->boolean ? $this->faker->dateTimeBetween($start_date, 'now') : null,
        ];
    }
}
