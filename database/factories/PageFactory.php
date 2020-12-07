<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Page;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker->addProvider(new \App\FakerProviders\Gutenberg($faker));

        return [
            'title' => $this->faker->sentence,
            'path' => $this->faker->unique()->parse($this->faker->randomElement(['{{slug}}/{{slug}}', '{{slug}}'])),
            'body' => $this->faker->gutenberg,
            'restricted' => false,
            'published_at' => now()->toDateTimeString(),
        ];
    }
}
