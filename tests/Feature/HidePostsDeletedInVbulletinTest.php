<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use HidePostsDeletedInVbulletin;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HidePostsDeletedInVbulletinTest extends TestCase
{
    /** @test */
    public function testThreadsDeletedInVbulletinAreSoftDeletedWithTheirPosts()
    {
        $thread = Thread::factory()->create(['id' => HidePostsDeletedInVbulletin::THREAD_IDS[0]]);
        $reply = Post::factory()->create(['thread_id' => $thread->id]);

        (new HidePostsDeletedInVbulletin)->up();

        $this->assertSoftDeleted($thread);
        $this->assertSoftDeleted($thread->posts()->withTrashed()->where('is_thread_initiator', true)->first());
        $this->assertSoftDeleted($reply);
    }

    /** @test */
    public function testPostsDeletedInVbulletinAreSoftDeleted()
    {
        $deletedPost = Post::factory()->create(['id' => HidePostsDeletedInVbulletin::POST_IDS[0]]);
        $keptPost = Post::factory()->create(['thread_id' => $deletedPost->thread_id]);
        $threadLastUpdatedAt = $deletedPost->thread->fresh()->updated_at;

        $this->travel(1)->days();

        (new HidePostsDeletedInVbulletin)->up();

        $this->assertSoftDeleted($deletedPost);
        $this->assertNotSoftDeleted($keptPost);
        $this->assertNotSoftDeleted($deletedPost->thread);
        $this->assertEquals($threadLastUpdatedAt, $deletedPost->thread->fresh()->updated_at);
    }

    /** @test */
    public function testActivityOfHiddenContentIsRemovedEvenWithoutASignedInUser()
    {
        $thread = Thread::factory()->create(['id' => HidePostsDeletedInVbulletin::THREAD_IDS[0]]);
        $deletedPost = Post::factory()->create(['id' => HidePostsDeletedInVbulletin::POST_IDS[0]]);
        $keptPost = Post::factory()->create();

        $thread->activity()->create(['user_id' => $thread->user_id, 'type' => 'created_thread']);
        $deletedPost->activity()->create(['user_id' => $deletedPost->user_id, 'type' => 'created_post']);
        $keptPost->activity()->create(['user_id' => $keptPost->user_id, 'type' => 'created_post']);

        $favorite = $deletedPost->favorites()->create(['user_id' => User::factory()->create()->id]);
        $favorite->activity()->create(['user_id' => $favorite->user_id, 'type' => 'created_favorite']);

        (new HidePostsDeletedInVbulletin)->up();

        $this->assertDatabaseMissing('activities', ['type' => 'created_thread']);
        $this->assertDatabaseMissing('activities', ['type' => 'created_favorite']);
        $this->assertDatabaseMissing('activities', ['subject_id' => $deletedPost->id, 'type' => 'created_post']);
        $this->assertDatabaseHas('activities', ['subject_id' => $keptPost->id, 'type' => 'created_post']);
    }

    /** @test */
    public function testAttachmentsAndFavoritesOfHiddenPostsAreKept()
    {
        Storage::fake('public');

        $post = Post::factory()->create(['id' => HidePostsDeletedInVbulletin::POST_IDS[0]]);
        $attachment = Attachment::factory()->create(['post_id' => $post->id]);
        $favorite = $post->favorites()->create(['user_id' => User::factory()->create()->id]);

        (new HidePostsDeletedInVbulletin)->up();

        $this->assertSoftDeleted($post);
        $this->assertDatabaseHas('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertExists($attachment->path);
        $this->assertDatabaseHas('favorites', ['id' => $favorite->id]);
    }

    /** @test */
    public function testReputationEarnedForHiddenContentIsTakenBack()
    {
        $thread = Thread::factory()->create(['id' => HidePostsDeletedInVbulletin::THREAD_IDS[0]]);
        $reply = Post::factory()->create(['id' => HidePostsDeletedInVbulletin::POST_IDS[0]]);

        $this->assertEquals(config('council.reputation.thread_published'), $thread->creator->fresh()->reputation);
        $this->assertEquals(config('council.reputation.reply_posted'), $reply->owner->fresh()->reputation);

        (new HidePostsDeletedInVbulletin)->up();

        $this->assertEquals(0, $thread->creator->fresh()->reputation);
        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }
}
