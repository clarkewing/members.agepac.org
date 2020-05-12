<?php

namespace Tests\Feature;

use App\Post;
use App\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BestPostTest extends TestCase
{
    /** @test */
    public function testThreadCreatorCanMarkAnyPostAsTheBestPost()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);
        $posts = create(Post::class, ['thread_id' => $thread->id], 2);

        $this->assertFalse($posts[1]->isBest());

        $this->postJson(route('posts.mark_best', [$posts[1]]));

        $this->assertTrue($posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testOnlyThreadCreatorMayMarkPostAsBest()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);
        $posts = create(Post::class, ['thread_id' => $thread->id], 2);

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

        $thread = create(Thread::class, ['user_id' => Auth::id()]);
        $posts = create(Post::class, ['thread_id' => $thread->id], 2);

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

        $post = create(Post::class, ['user_id' => Auth::id()]);

        $post->thread->markBestPost($post);

        $this->deleteJson(route('posts.destroy', $post));

        $this->assertNull($post->thread->fresh()->best_post_id);
    }
}
