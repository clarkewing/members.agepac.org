<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Cashier\Subscription;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

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
            'name' => 'membership',
            'stripe_id' => $this->faker->md5, // Random string
            'stripe_status' => 'active',
            'stripe_plan' => $this->faker->randomElement(config('council.plans')),
            'quantity' => 1,
        ];
    }
}
