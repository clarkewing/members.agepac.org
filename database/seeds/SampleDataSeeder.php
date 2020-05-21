<?php

use App\Activity;
use App\Attachment;
use App\Channel;
use App\Favorite;
use App\Post;
use App\Thread;
use App\ThreadSubscription;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
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
        Post::truncate();
        Attachment::truncate();
        (new Filesystem)->cleanDirectory('storage/app/public/attachments');
        ThreadSubscription::truncate();
        Activity::truncate();
        Favorite::truncate();

        factory(Thread::class, 30)->states('from_existing_channel_and_user')->create()
            ->each(function ($thread) {
                $this->recordActivity($thread, 'created', $thread->creator->id);

                factory(Post::class, $this->faker->numberBetween(1, 10))
                    ->states($this->faker->boolean(10)
                        ? ['from_existing_user']
                        : ['from_existing_user', 'with_attachment']
                    )->create([
                        'thread_id' => $thread->id,
                    ])->each(function ($post) {
                        $this->recordActivity($post, 'created', $post->owner->id);
                    });
            });
    }

    /**
     * @param $model
     * @param $event_type
     * @param $user_id
     *
     * @throws ReflectionException
     */
    protected function recordActivity($model, $event_type, $user_id)
    {
        $type = strtolower((new \ReflectionClass($model))->getShortName());

        $model->morphMany(Activity::class, 'subject')->create([
            'user_id' => $user_id,
            'type' => "{$event_type}_{$type}",
        ]);
    }
}
