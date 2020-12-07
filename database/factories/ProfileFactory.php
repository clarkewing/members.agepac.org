<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Location;
use App\Profile;
use App\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function ($profile) {
            $profile->location()->save(Location::factory()->make());
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return User::factory()->raw([
            'bio' => $this->faker->paragraph,
            'flight_hours' => $this->faker->numberBetween(0, 15000),
        ]);
    }
}
