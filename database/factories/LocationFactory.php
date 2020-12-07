<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Location;
use App\User;
use Illuminate\Support\Arr;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'locatable_id' => function () {
                return User::factory()->create()->id;
            },
            'locatable_type' => \App\User::class,
            'type' => Arr::random(['country', 'city', 'address', 'busStop', 'trainStation', 'townhall', 'airport']),
            'name' => $this->faker->sentence,
            'street_line_1' => $this->faker->streetAddress,
            'street_line_2' => $this->faker->secondaryAddress,
            'municipality' => $this->faker->city,
            'administrative_area' => $this->faker->region,
            'sub_administrative_area' => null,
            'postal_code' => $this->faker->postcode,
            'country' => $country = $this->faker->country,
            'country_code' => function () {
                \Locale::setDefault(config('app.locale'));

                return array_rand(\Symfony\Component\Intl\Countries::getNames());
            },
        ];
    }
}
