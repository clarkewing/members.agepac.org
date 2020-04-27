<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ParticipateInThreadsTest extends TestCase
{
    /**
     * @test
     */
    public function testUnauthenticatedUsersMayNotAddReplies()
    {
        $this->withExceptionHandling()
            ->post('threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function testAuthenticatedUserMayParticipateInForumThreads()
    {
        $this->signIn();

        $thread = create(Thread::class);
        $reply = make(Reply::class);

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
    }

//    /**
//     * @test
//     */
//    public function testRepliesThatContainSpamMayNotBeCreated()
//    {
//        $this->withExceptionHandling();
//        $this->signIn();
//
//        $thread = create(Thread::class);
//        $reply = make(Reply::class, [
//            'body' => 'Yahoo Customer Support',
//        ]);
//
//        $this->json('post', $thread->path() . '/replies', $reply->toArray())
//            ->assertStatus(422);
//    }

    /**
     * @test
     */
    public function testReplyRequiresABody()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create(Thread::class);
        $reply = make(Reply::class, ['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /**
     * @test
     */
    public function testUnauthorizedUsersCannotDeleteReplies()
    {
        $this->withExceptionHandling();

        $reply = create(Reply::class);

        $this->delete(route('replies.destroy', $reply))
            ->assertRedirect('/login');

        $this->signIn()
            ->delete(route('replies.destroy', $reply))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function testAuthorizedUsersCanDeleteReplies()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => Auth::id()]);

        $this->delete(route('replies.destroy', $reply))
            ->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    /**
     * @test
     */
    public function testUnauthorizedUsersCannotUpdateReplies()
    {
        $this->withExceptionHandling();

        $reply = create(Reply::class);

        $this->patch(route('replies.update', $reply))
            ->assertRedirect('/login');

        $this->signIn()
            ->patch(route('replies.update', $reply))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function testAuthorizedUsersCanUpdateReplies()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => Auth::id()]);

        $updatedReply = 'You been changed, fool.';
        $this->patch(route('replies.update', $reply), ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedReply,
        ]);
    }

    /**
     * @test
     */
    public function testUsersMayOnlyReplyAMaximumOfOncePerMinute()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create(Thread::class);

        $reply = make(Reply::class, [
            'body' => 'My simple reply.',
        ]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(201);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429); // Too many requests
    }
}
