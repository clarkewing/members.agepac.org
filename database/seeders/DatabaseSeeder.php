<?php

namespace Database\Seeders;

use App\Activity;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Activity::truncate();

        $this->call([
            UserSeeder::class,
            ForumDataSeeder::class,
            ProfileDataSeeder::class,
        ]);
    }
}
