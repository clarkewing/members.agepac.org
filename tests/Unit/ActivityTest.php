<?php

namespace Tests\Unit;

use App\User;
use App\Reply;
use App\Thread;
use App\Activity;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    /**
     * @test
     */
    public function testRecordsActivityWhenAThreadIsCreated()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => Auth::id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread',
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $thread->id);
    }

    /**
     * @test
     */
    public function testRecordsActivityWhenAReplyIsCreated()
    {
        $this->signIn();

        // Will also create associated thread.
        $thread = create(Reply::class);

        $this->assertEquals(2, Activity::count());
    }

    /**
     * @test
     */
    public function testFetchesAnActivityFeedForAnyUser()
    {
        $this->signIn();

        create(Thread::class, ['user_id' => Auth::id()], 2);
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
