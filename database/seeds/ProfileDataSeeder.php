<?php

use App\Location;
use App\Occupation;
use App\User;
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
            ->occupations();

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Seed user locations.
     */
    protected function locations()
    {
        Location::where('locatable_id', 'App\User')->delete();

        User::inRandomOrder()->take(25)->each(function ($user) {
            factory(Location::class)
                ->create([
                    'locatable_id' => $user->id,
                    'locatable_type' => get_class($user),
                ]);
        });

        return $this;
    }

    /**
     * Seed the occupations table.
     */
    protected function occupations()
    {
        Occupation::truncate();
        Location::where('locatable_id', 'App\Occupation')->delete();

        User::inRandomOrder()->take(25)->each(function ($user) {
            factory(Occupation::class, $this->faker->numberBetween(1, 10))
                ->create(['user_id' => $user->id]);
        });

        return $this;
    }
}
