<?php

namespace Database\Factories\Illuminate\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class DatabaseNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DatabaseNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'type' => \App\Notifications\ThreadWasUpdated::class,
            'notifiable_id' => function () {
                return Auth::id() ?? User::factory()->create()->id;
            },
            'notifiable_type' => (new User)->getMorphClass(),
            'data' => ['foo' => 'bar'],
        ];
    }
}
