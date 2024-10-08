<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
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
            'locatable_type' => (new User)->getMorphClass(),
            'type' => Arr::random(['administrative', 'airport', 'bus', 'city', 'country', 'county', 'street', 'tourism', 'townhall', 'train']),
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
