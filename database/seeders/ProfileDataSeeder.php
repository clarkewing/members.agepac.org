<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Location;
use App\Models\Occupation;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProfileDataSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->locations()
            ->resumes();

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Seed user locations.
     */
    protected function locations()
    {
        Location::where('locatable_type', \App\Models\User::class)->delete();

        User::inRandomOrder()->take(25)->each(function ($user) {
            Location::factory()
                ->create([
                    'locatable_id' => $user->id,
                    'locatable_type' => get_class($user),
                ]);
        });

        return $this;
    }

    /**
     * Seed user resumes.
     */
    protected function resumes()
    {
        Occupation::truncate();
        Course::truncate();
        Location::whereIn('locatable_type', [\App\Models\Occupation::class, \App\Models\Course::class])->delete();

        User::inRandomOrder()->take(25)->each(function ($user) {
            Occupation::factory()->count($this->faker->numberBetween(1, 10))
                ->create(['user_id' => $user->id]);

            Course::factory()->count($this->faker->numberBetween(1, 10))
                ->create(['user_id' => $user->id]);
        });

        return $this;
    }
}
