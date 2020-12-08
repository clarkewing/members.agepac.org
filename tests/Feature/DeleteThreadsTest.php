<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteThreadsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testUnauthorizedUsersMayNotDeleteThreads()
    {
        $thread = Thread::factory()->create();

        $this->delete($thread->path())
            ->assertForbidden();
    }

    /** @test */
    public function testAThreadCanBeDeletedByAnAuthorizedUser()
    {
        $thread = Thread::factory()->create();
        $post = Post::factory()->create(['thread_id' => $thread->id]);

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
        $thread = Thread::factory()->create(['user_id' => Auth::id()]);

        Post::factory()->create(['thread_id' => $thread->id]);

        $this->delete($thread->path())->assertForbidden();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'deleted_at' => null]);
    }

    /** @test */
    public function testAThreadWithNoRepliesCanBeDeletedByItsCreator()
    {
        $thread = Thread::factory()->create(['user_id' => Auth::id()]);

        $this->delete($thread->path());

        $this->assertSoftDeleted($thread);
    }
}
