<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    /** @test */
    public function testDoesntRecordActivityIfNoAuthenticatedUser()
    {
        $post = Post::factory()->create();

        $this->assertDatabaseMissing('activities', [
            'subject_id' => $post->id,
            'subject_type' => array_flip(Relation::$morphMap)[get_class($post)],
        ]);
    }

    /** @test */
    public function testRecordsActivityWhenUserIsCreated()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_user',
            'user_id' => $user->id,
            'subject_id' => $user->id,
            'subject_type' => array_flip(Relation::$morphMap)[get_class($user)],
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
            'subject_type' => array_flip(Relation::$morphMap)[get_class($profile)],
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
            'subject_type' => array_flip(Relation::$morphMap)[get_class($thread)],
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
            'subject_type' => array_flip(Relation::$morphMap)[get_class($post)],
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

    /** @test */
    public function testDeletingAUserDeletesAssociatedActivities()
    {
        $user = User::factory()->create();
        $this->signIn($user);

        $this->assertDatabaseHas('activities', $createdUserActivity = [
            'type' => 'created_user',
            'user_id' => $user->id,
            'subject_id' => $user->id,
            'subject_type' => array_flip(Relation::$morphMap)[get_class($user)],
        ]);

        $user->profile->update(['bio' => 'This should trigger an update.']);

        $this->assertDatabaseHas('activities', $updatedProfileActivity = [
            'type' => 'updated_profile',
            'user_id' => $user->id,
            'subject_id' => $user->profile->id,
            'subject_type' => array_flip(Relation::$morphMap)[get_class($user->profile)],
        ]);

        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('activities', $createdThreadActivity = [
            'type' => 'created_thread',
            'user_id' => $user->id,
            'subject_id' => $thread->id,
            'subject_type' => array_flip(Relation::$morphMap)[get_class($thread)],
        ]);

        $post = $thread->addPost([
            'user_id' => $user->id,
            'body' => 'Here is a post.',
        ]);

        $this->assertDatabaseHas('activities', $createdPostActivity = [
            'type' => 'created_post',
            'subject_id' => $post->id,
            'subject_type' => array_flip(Relation::$morphMap)[get_class($post)],
        ]);

        $user->delete();

        $this->assertDatabaseMissing('activities', $createdUserActivity);

        $this->assertDatabaseMissing('activities', $updatedProfileActivity);

        $this->assertDatabaseMissing('activities', $createdThreadActivity);

        $this->assertDatabaseMissing('activities', $createdPostActivity);
    }
}
