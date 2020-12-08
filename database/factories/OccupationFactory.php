<?php

namespace Database\Factories;

use App\Models\Aircraft;
use App\Models\Company;
use App\Models\Location;
use App\Models\Occupation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class OccupationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Occupation::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function ($occupation) {
            Location::factory()->create([
                'locatable_id' => $occupation->id,
                'locatable_type' => get_class($occupation),
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
        $is_pilot = $this->faker->boolean;

        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'position' => $is_pilot ? Arr::random(['CDB', 'OPL']) : $this->faker->jobTitle,
            'aircraft_id' => $is_pilot ? Aircraft::all()->random()->id : null,
            'company_id' => function () {
                return Company::factory()->create()->id;
            },
            'status' => Arr::random(array_keys(Occupation::statusStrings())),
            'description' => $this->faker->paragraph,
            'start_date' => $start_date = $this->faker->date,
            'end_date' => $this->faker->boolean ? $this->faker->dateTimeBetween($start_date, 'now') : null,
            'is_primary' => false,
        ];
    }

    public function pilot()
    {
        return $this->state(['position' => Arr::random(['CDB', 'OPL']), 'aircraft_id' => Aircraft::all()->random()->id]);
    }

    public function notPilot()
    {
        return $this->state(function () {
            return [
                'position' => $this->faker->jobTitle,
                'aircraft_id' => null,
            ];
        });
    }

    public function past()
    {
        return $this->state(function () {
            return [
                'start_date' => $start_date = $this->faker->date,
                'end_date' => $this->faker->dateTimeBetween($start_date, 'now'),
            ];
        });
    }

    public function current()
    {
        return $this->state(function () {
            return [
                'end_date' => null,
            ];
        });
    }
}
