<?php

use App\Activity;
use App\Channel;
use App\Favorite;
use App\Reply;
use App\Thread;
use App\ThreadSubscription;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SampleDataSeeder extends Seeder
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

        $this->channels()->content();

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Seed the channels table.
     */
    protected function channels()
    {
        Channel::truncate();

        factory(Channel::class, 10)->create();

        return $this;
    }

    /**
     * Seed the thread-related tables.
     */
    protected function content()
    {
        Thread::truncate();
        Reply::truncate();
        ThreadSubscription::truncate();
        Activity::truncate();
        Favorite::truncate();

        factory(Thread::class, 50)->create()
            ->each(function ($thread) {
                factory(Reply::class, $this->faker->numberBetween(0, 10))
                    ->create([
                        'thread_id' => $thread->id,
                    ]);
            });
    }
}
