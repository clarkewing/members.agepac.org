<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BestPostTest extends TestCase
{
    /** @test */
    public function testThreadCreatorCanMarkAnyPostAsTheBestPost()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['user_id' => Auth::id()]);
        $posts = Post::factory()->count(2)->create(['thread_id' => $thread->id]);

        $this->assertFalse($posts[1]->isBest());

        $this->postJson(route('posts.mark_best', [$posts[1]]));

        $this->assertTrue($posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testThreadCreatorCanUnmarkBestPost()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['user_id' => Auth::id()]);
        $posts = Post::factory()->count(2)->create(['thread_id' => $thread->id]);

        $thread->markBestPost($posts[1]);
        $this->assertTrue($posts[1]->fresh()->isBest());

        $this->deleteJson(route('posts.unmark_best', $posts[1]));

        $this->assertFalse($posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testOnlyThreadCreatorMayMarkPostAsBest()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = Thread::factory()->create(['user_id' => Auth::id()]);
        $posts = Post::factory()->count(2)->create(['thread_id' => $thread->id]);

        $this->signIn();

        $this->postJson(route('posts.mark_best', $posts[1]))
            ->assertStatus(403);

        $this->assertFalse($posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testOnlyThreadCreatorMayUnmarkPostAsBest()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = Thread::factory()->create(['user_id' => Auth::id()]);
        $posts = Post::factory()->count(2)->create(['thread_id' => $thread->id]);

        $thread->markBestPost($posts[1]);

        $this->signIn();

        $this->deleteJson(route('posts.unmark_best', $posts[1]))
            ->assertStatus(403);

        $this->assertTrue($posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testIfABestPostIsDeletedThenTheThreadIsProperlyUpdated()
    {
        $this->signIn();

        $post = Post::factory()->create(['user_id' => Auth::id()]);

        $post->thread->markBestPost($post);

        $this->deleteJson(route('posts.destroy', $post));

        $this->assertNull($post->thread->fresh()->best_post_id);
    }
}
