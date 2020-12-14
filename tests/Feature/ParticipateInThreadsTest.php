<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ParticipateInThreadsTest extends TestCase
{
    /** @test */
    public function testUnauthenticatedUsersMayNotAddPosts()
    {
        $this->withExceptionHandling()
            ->post('threads/some-channel/1/posts', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function testAuthenticatedUserMayParticipateInForumThreads()
    {
        $this->signIn();

        $thread = Thread::factory()->create();
        $post = Post::factory()->make();

        $this->post($thread->path() . '/posts', $post->toArray())
            ->assertStatus(201);

        $this->assertDatabaseHas('posts', ['body' => "<p>{$post->body}</p>"]);
    }

//    /**
//     * @test
//     */
//    public function testPostsThatContainSpamMayNotBeCreated()
//    {
//        $this->withExceptionHandling();
//        $this->signIn();
//
//        $thread = Thread::factory()->create();
//
//        $this->json('post', $thread->path() . '/posts', Post::factory()->raw([
//            'body' => 'Yahoo Customer Support',
//        ]))
//            ->assertStatus(422);
//    }

    /** @test */
    public function testPostRequiresABody()
    {
        $this->withExceptionHandling()->signIn();

        $thread = Thread::factory()->create();
        $this->post($thread->path() . '/posts', Post::factory()->raw(['body' => null]))
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeletePosts()
    {
        $this->withExceptionHandling();

        $post = Post::factory()->create();

        $this->delete(route('posts.destroy', $post))
            ->assertRedirect('/login');

        $this->signIn()
            ->delete(route('posts.destroy', $post))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function testUsersCanDeleteTheirOwnPosts()
    {
        $this->signIn();

        $post = Post::factory()->create(['user_id' => Auth::id()]);

        $this->deleteJson(route('posts.destroy', $post))
            ->assertSuccessful();

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /** @test */
    public function testAuthorizedUsersCanDeletePosts()
    {
        $this->signInWithPermission('posts.delete');

        $post = Post::factory()->create();

        $this->deleteJson(route('posts.destroy', $post))
            ->assertSuccessful();

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /** @test */
    public function testThreadInitiatorPostCannotBeDeleted()
    {
        $this->withExceptionHandling()
            ->signIn();

        $post = Post::factory()->create([
            'user_id' => Auth::id(),
            'is_thread_initiator' => true,
        ]);

        $this->delete(route('posts.destroy', $post))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'deleted_at' => null]);
    }

    /** @test */
    public function testUnauthorizedUsersCannotRestorePosts()
    {
        $this->withExceptionHandling();

        ($post = Post::factory()->create())->delete();

        $this->patch(route('posts.update', $post), ['deleted_at' => null])
            ->assertRedirect('/login');

        $this->signIn()
            ->patch(route('posts.update', $post), ['deleted_at' => null])
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function testAuthorizedUsersCanRestorePosts()
    {
        $this->signInWithPermission('posts.restore');

        ($post = Post::factory()->create())->delete();

        $this->patchJson(route('posts.update', $post), ['deleted_at' => null])
            ->assertSuccessful();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'deleted_at' => null]);
    }

    /** @test */
    public function testUnauthorizedUsersCannotUpdatePosts()
    {
        $this->withExceptionHandling();

        $post = Post::factory()->create();

        $this->patch(route('posts.update', $post))
            ->assertRedirect('/login');

        $this->signIn()
            ->patch(route('posts.update', $post))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function testUsersCanUpdateTheirOwnPosts()
    {
        $this->signIn();

        $post = Post::factory()->create(['user_id' => Auth::id()]);

        $updatedPost = 'You been changed, fool.';
        $this->patch(route('posts.update', $post), ['body' => $updatedPost]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'body' => "<p>$updatedPost</p>",
        ]);
    }

    /** @test */
    public function testAuthorizedUsersCanUpdatePosts()
    {
        $this->signInWithPermission('posts.edit');

        $post = Post::factory()->create();

        $updatedPost = 'You been changed, fool.';
        $this->patch(route('posts.update', $post), ['body' => $updatedPost]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'body' => "<p>$updatedPost</p>",
        ]);
    }

    /** @test */
    public function testUsersMayOnlyPostAMaximumOfOncePerMinute()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = Thread::factory()->create();

        $post = Post::factory()->raw([
            'body' => 'My simple post.',
        ]);

        $this->post($thread->path() . '/posts', $post)
            ->assertStatus(201);

        $this->post($thread->path() . '/posts', $post)
            ->assertStatus(429); // Too many requests
    }
}
