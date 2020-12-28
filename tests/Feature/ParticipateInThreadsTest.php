<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ParticipateInThreadsTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function testGuestsCannotAddPosts()
    {
        Auth::logout();

        $this->publishPost()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotAddPosts()
    {
        $this->signInUnsubscribed();

        $this->publishPost()
            ->assertPaymentRequired();
    }

    /** @test */
    public function testSubscribedUserCanAddPost()
    {
        $post = Post::factory()->make();

        $this->publishPost($post->toArray())
            ->assertCreated();

        $this->assertDatabaseHas('posts', ['body' => "<p>{$post->body}</p>"]);
    }

//    /**
//     * @test
//     */
//    public function testPostsThatContainSpamMayNotBeCreated()
//    {
//        $this->publishPost(Post::factory()->raw([
//            'body' => 'Yahoo Customer Support',
//        ]))
//            ->assertStatus(422);
//    }

    /** @test */
    public function testPostRequiresABody()
    {
        $this->publishPost(Post::factory()->raw(['body' => null]))
            ->assertJsonValidationErrors('body');
    }

    /** @test */
    public function testGuestsCannotDeletePosts()
    {
        Auth::logout();

        $this->deletePost()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotDeletePosts()
    {
        $this->signInUnsubscribed();

        $this->deletePost()
            ->assertPaymentRequired();
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeletePosts()
    {
        $this->deletePost()
            ->assertForbidden();
    }

    /** @test */
    public function testUsersCanDeleteTheirOwnPosts()
    {
        $post = Post::factory()->create(['user_id' => Auth::id()]);

        $this->deletePost($post)
            ->assertSuccessful();

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /** @test */
    public function testAuthorizedUsersCanDeletePosts()
    {
        $this->signInWithPermission('posts.delete');

        $post = Post::factory()->create();

        $this->deletePost($post)
            ->assertSuccessful();

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /** @test */
    public function testThreadInitiatorPostCannotBeDeleted()
    {
        $post = Post::factory()->create([
            'user_id' => Auth::id(),
            'is_thread_initiator' => true,
        ]);

        $this->deletePost($post)
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'deleted_at' => null]);
    }

    /** @test */
    public function testGuestsCannotRestorePosts()
    {
        Auth::logout();

        $post = tap(Post::factory()->create())->delete();

        $this->restorePost($post)
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotRestorePosts()
    {
        $this->signInUnsubscribed();

        $post = tap(Post::factory()->create())->delete();

        $this->restorePost($post)
            ->assertPaymentRequired();
    }

    /** @test */
    public function testUnauthorizedUsersCannotRestorePosts()
    {
        $post = tap(Post::factory()->create())->delete();

        $this->restorePost($post)
            ->assertNotFound();
    }

    /** @test */
    public function testAuthorizedUsersCanRestorePosts()
    {
        $this->signInWithPermission('posts.restore');

        $post = tap(Post::factory()->create())->delete();

        $this->restorePost($post)
            ->assertSuccessful();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'deleted_at' => null]);
    }

    /** @test */
    public function testGuestsCannotUpdatePosts()
    {
        Auth::logout();

        $this->updatePost()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotUpdatePosts()
    {
        $this->signInUnsubscribed();

        $this->updatePost()
            ->assertPaymentRequired();
    }

    /** @test */
    public function testUnauthorizedUsersCannotUpdatePosts()
    {
        $this->withExceptionHandling()->signIn();

        $this->updatePost()
            ->assertForbidden();
    }

    /** @test */
    public function testUsersCanUpdateTheirOwnPosts()
    {
        $post = Post::factory()->create(['user_id' => Auth::id()]);

        $updatedBody = 'You been changed, fool.';

        $this->updatePost(['body' => $updatedBody], $post);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'body' => "<p>$updatedBody</p>",
        ]);
    }

    /** @test */
    public function testAuthorizedUsersCanUpdatePosts()
    {
        $this->signInWithPermission('posts.edit');

        $post = Post::factory()->create();

        $updatedBody = 'You been changed, fool.';

        $this->patch(route('posts.update', $post), ['body' => $updatedBody]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'body' => "<p>$updatedBody</p>",
        ]);
    }

    /** @test */
    public function testUsersMayOnlyPostAMaximumOfOncePerMinute()
    {
        $post = Post::factory()->raw([
            'body' => 'My simple post.',
        ]);

        $this->publishPost($post)
            ->assertCreated();

        $this->publishPost($post)
            ->assertStatus(429); // Too many requests
    }

    /**
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function publishPost(array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson($this->thread->path() . '/posts', $data);
    }

    /**
     * @param  \App\Models\Post|null  $post
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deletePost(Post $post = null): \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('posts.destroy', $post ?? Post::factory()->create()));
    }

    /**
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Testing\TestResponse
     */
    protected function restorePost(Post $post): \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(route('posts.update', $post), ['deleted_at' => null]);
    }

    /**
     * @param  array  $data
     * @param  \App\Models\Post|null  $post
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updatePost(array $data = [], Post $post = null): \Illuminate\Testing\TestResponse
    {
        return $this->patchJson(
            route('posts.update', $post ?? Post::factory()->create()),
            $data
        );
    }
}
