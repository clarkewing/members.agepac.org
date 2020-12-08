<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    /** @test */
    public function testRecordsActivityWhenUserIsCreated()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_user',
            'user_id' => $user->id,
            'subject_id' => $user->id,
            'subject_type' => \App\Models\User::class,
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $user->id);
    }

    /** @test */
    public function testRecordsActivityWhenProfileIsUpdated()
    {
        $this->signIn();

        $profile = Auth::user()->profile;

        $profile->update(['bio' => 'This should trigger an update.']);

        $this->assertDatabaseHas('activities', [
            'type' => 'updated_profile',
            'user_id' => Auth::id(),
            'subject_id' => $profile->id,
            'subject_type' => \App\Profile::class,
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $profile->id);
    }

    /** @test */
    public function testRecordsActivityWhenAThreadIsCreated()
    {
        $this->signIn();

        $thread = Thread::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => Auth::id(),
            'subject_id' => $thread->id,
            'subject_type' => \App\Models\Thread::class,
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $thread->id);
    }

    /** @test */
    public function testRecordsActivityWhenAPostIsCreated()
    {
        $this->signIn();

        // Will also create associated thread.
        $post = Post::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_post',
            'subject_id' => $post->id,
            'subject_type' => \App\Models\Post::class,
        ]);
    }

    /** @test */
    public function testFetchesAnActivityFeedForAnyUser()
    {
        $this->signIn();

        Thread::factory()->count(2)->create(['user_id' => Auth::id()]);
        Auth::user()->activity()->first()->update(['created_at' => now()->subWeek()]);

        $feed = Activity::feed(Auth::user());

        $this->assertTrue($feed->keys()->contains(
            now()->format('Y-m-d')
        ));
        $this->assertTrue($feed->keys()->contains(
            now()->subWeek()->format('Y-m-d')
        ));
    }
}
