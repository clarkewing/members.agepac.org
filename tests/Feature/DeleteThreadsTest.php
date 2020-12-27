<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteThreadsTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = Thread::factory()->create(['user_id' => Auth::id()]);
    }

    /** @test */
    public function testGuestsCannotDeleteThreads()
    {
        Auth::logout();

        $this->deleteThread()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotDeleteThreads()
    {
        $this->signInUnsubscribed();

        $this->deleteThread()
            ->assertPaymentRequired();
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteThreads()
    {
        $this->signIn();

        $this->deleteThread()
            ->assertForbidden();
    }

    /** @test */
    public function testAThreadCanBeDeletedByAnAuthorizedUser()
    {
        $post = Post::factory()->create(['thread_id' => $this->thread->id]);

        $this->signInWithPermission('threads.delete');

        $this->deleteThread()->assertNoContent();

        $this->assertSoftDeleted($this->thread);
        $this->assertSoftDeleted($post);

        $this->assertDatabaseMissing('activities', ['type' => 'created_thread']);
        $this->assertDatabaseMissing('activities', ['type' => 'created_post']);
    }

    /** @test */
    public function testAThreadWithRepliesCannotBeDeletedByItsCreator()
    {
        Post::factory()->create(['thread_id' => $this->thread->id]);

        $this->deleteThread()->assertForbidden();

        $this->assertDatabaseHas('threads', ['id' => $this->thread->id, 'deleted_at' => null]);
    }

    /** @test */
    public function testAThreadWithNoRepliesCanBeDeletedByItsCreator()
    {
        $this->deleteThread();

        $this->assertSoftDeleted($this->thread);
    }

    /**
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deleteThread(): \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson($this->thread->path());
    }
}
