<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'type_code' => $this->faker->randomKey(Company::typeStrings()),
            'website' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'operations' => $this->faker->paragraph(),
            'conditions' => $this->faker->paragraph(),
            'remarks' => $this->faker->paragraph(),
        ];
    }
}
