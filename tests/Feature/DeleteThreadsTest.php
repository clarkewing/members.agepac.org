<?php

namespace Tests\Feature;

use App\Post;
use App\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteThreadsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testUnauthorizedUsersMayNotDeleteThreads()
    {
        $thread = create(Thread::class);

        $this->delete($thread->path())
            ->assertForbidden();
    }

    /** @test */
    public function testAThreadCanBeDeletedByAnAuthorizedUser()
    {
        $thread = create(Thread::class);
        $post = create(Post::class, ['thread_id' => $thread->id]);

        $this->signInWithPermission('threads.delete');

        $this->deleteJson($thread->path())->assertNoContent();

        $this->assertSoftDeleted($thread);
        $this->assertSoftDeleted($post);

        $this->assertDatabaseMissing('activities', ['type' => 'created_thread']);
        $this->assertDatabaseMissing('activities', ['type' => 'created_post']);
    }

    /** @test */
    public function testAThreadWithRepliesCannotBeDeletedByItsCreator()
    {
        $thread = create(Thread::class, ['user_id' => Auth::id()]);

        create(Post::class, ['thread_id' => $thread->id]);

        $this->delete($thread->path())->assertForbidden();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'deleted_at' => null]);
    }

    /** @test */
    public function testAThreadWithNoRepliesCanBeDeletedByItsCreator()
    {
        $thread = create(Thread::class, ['user_id' => Auth::id()]);

        $this->delete($thread->path());

        $this->assertSoftDeleted($thread);
    }
}
