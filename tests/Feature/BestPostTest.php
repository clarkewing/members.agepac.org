<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BestPostTest extends TestCase
{
    protected $thread;

    protected $posts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = Thread::factory()->create(['user_id' => Auth::id()]);
        $this->posts = Post::factory()->count(2)->create(['thread_id' => $this->thread->id]);
    }

    /** @test */
    public function testGuestCannotMarkBestPost()
    {
        Auth::logout();

        $this->postJson(route('posts.mark_best', [$this->posts[1]]))
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUserCannotMarkBestPost()
    {
        $this->signInUnsubscribed();

        $this->postJson(route('posts.mark_best', [$this->posts[1]]))
            ->assertPaymentRequired();
    }

    /** @test */
    public function testThreadCreatorCanMarkAnyPostAsTheBestPost()
    {
        $this->assertFalse($this->posts[1]->isBest());

        $this->postJson(route('posts.mark_best', [$this->posts[1]]));

        $this->assertTrue($this->posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testThreadCreatorCanUnmarkBestPost()
    {
        $this->thread->markBestPost($this->posts[1]);
        $this->assertTrue($this->posts[1]->fresh()->isBest());

        $this->deleteJson(route('posts.unmark_best', $this->posts[1]));

        $this->assertFalse($this->posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testOnlyThreadCreatorMayMarkPostAsBest()
    {
        $this->signIn();

        $this->postJson(route('posts.mark_best', $this->posts[1]))
            ->assertStatus(403);

        $this->assertFalse($this->posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testOnlyThreadCreatorMayUnmarkPostAsBest()
    {
        $this->thread->markBestPost($this->posts[1]);

        $this->signIn();

        $this->deleteJson(route('posts.unmark_best', $this->posts[1]))
            ->assertStatus(403);

        $this->assertTrue($this->posts[1]->fresh()->isBest());
    }

    /** @test */
    public function testIfABestPostIsDeletedThenTheThreadIsProperlyUpdated()
    {
        $post = Post::factory()->create(['user_id' => Auth::id()]);

        $post->thread->markBestPost($post);

        $this->deleteJson(route('posts.destroy', $post));

        $this->assertNull($post->thread->fresh()->best_post_id);
    }
}
