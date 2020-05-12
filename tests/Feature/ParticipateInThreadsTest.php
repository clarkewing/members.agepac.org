<?php

namespace Tests\Feature;

use App\Post;
use App\Thread;
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

        $thread = create(Thread::class);
        $post = make(Post::class);

        $this->post($thread->path() . '/posts', $post->toArray());

        $this->assertDatabaseHas('posts', ['body' => $post->body]);
    }

//    /**
//     * @test
//     */
//    public function testPostsThatContainSpamMayNotBeCreated()
//    {
//        $this->withExceptionHandling();
//        $this->signIn();
//
//        $thread = create(Thread::class);
//        $post = make(Post::class, [
//            'body' => 'Yahoo Customer Support',
//        ]);
//
//        $this->json('post', $thread->path() . '/posts', $post->toArray())
//            ->assertStatus(422);
//    }

    /** @test */
    public function testPostRequiresABody()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create(Thread::class);
        $post = make(Post::class, ['body' => null]);

        $this->post($thread->path() . '/posts', $post->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeletePosts()
    {
        $this->withExceptionHandling();

        $post = create(Post::class);

        $this->delete(route('posts.destroy', $post))
            ->assertRedirect('/login');

        $this->signIn()
            ->delete(route('posts.destroy', $post))
            ->assertStatus(403);
    }

    /** @test */
    public function testAuthorizedUsersCanDeletePosts()
    {
        $this->signIn();

        $post = create(Post::class, ['user_id' => Auth::id()]);

        $this->delete(route('posts.destroy', $post))
            ->assertStatus(302);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function testThreadInitiatorPostCannotBeDeleted()
    {
        $this->withExceptionHandling()
            ->signIn();

        $post = create(Post::class, [
            'user_id' => Auth::id(),
            'is_thread_initiator' => true,
        ]);

        $this->delete(route('posts.destroy', $post))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    /** @test */
    public function testUnauthorizedUsersCannotUpdatePosts()
    {
        $this->withExceptionHandling();

        $post = create(Post::class);

        $this->patch(route('posts.update', $post))
            ->assertRedirect('/login');

        $this->signIn()
            ->patch(route('posts.update', $post))
            ->assertStatus(403);
    }

    /** @test */
    public function testAuthorizedUsersCanUpdatePosts()
    {
        $this->signIn();

        $post = create(Post::class, ['user_id' => Auth::id()]);

        $updatedPost = 'You been changed, fool.';
        $this->patch(route('posts.update', $post), ['body' => $updatedPost]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'body' => $updatedPost,
        ]);
    }

    /** @test */
    public function testUsersMayOnlyPostAMaximumOfOncePerMinute()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create(Thread::class);

        $post = make(Post::class, [
            'body' => 'My simple post.',
        ]);

        $this->post($thread->path() . '/posts', $post->toArray())
            ->assertStatus(201);

        $this->post($thread->path() . '/posts', $post->toArray())
            ->assertStatus(429); // Too many requests
    }
}
