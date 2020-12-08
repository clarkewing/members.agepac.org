<?php

namespace Tests\Feature;

use App\Models\Thread;
use Tests\TestCase;

class PinThreadsTest extends TestCase
{
    /** @test */
    public function testUnauthorizedUsersCannotPinThreads()
    {
        $this->withExceptionHandling()->signIn();

        $thread = Thread::factory()->create();

        $this->post(route('threads.pin', $thread))
            ->assertStatus(403);

        $this->assertFalse($thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /** @test */
    public function testUnauthorizedUsersCannotUnpinThreads()
    {
        $this->withExceptionHandling()->signIn();

        $thread = Thread::factory()->create(['pinned' => true]);

        $this->delete(route('threads.unpin', $thread))
            ->assertStatus(403);

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function testAuthorizedUsersCanPinThreads()
    {
        $this->signInWithPermission('threads.pin');

        $thread = Thread::factory()->create();

        $this->post(route('threads.pin', $thread))
            ->assertStatus(204);

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function testAuthorizedUsersCanUnpinThreads()
    {
        $this->signInWithPermission('threads.unpin');

        $thread = Thread::factory()->create(['pinned' => true]);

        $this->delete(route('threads.unpin', $thread))
            ->assertStatus(204);

        $this->assertFalse($thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /** @test */
    public function testPinnedThreadsAreListedFirst()
    {
        $this->signInWithPermission('threads.pin');

        [$threadOne, $threadTwo, $threadThree] = Thread::factory()->count(3)->create();

        $this->getJson(route('threads.index'))->assertJson([
            'data' => [
                ['id' => $threadOne->id],
                ['id' => $threadTwo->id],
                ['id' => $threadThree->id],
            ],
        ]);

        $this->post(route('threads.pin', $pinned = $threadThree))
            ->assertStatus(204);

        $this->getJson(route('threads.index'))->assertJson([
            'data' => [
                ['id' => $pinned->id],
                ['id' => $threadOne->id],
                ['id' => $threadTwo->id],
            ],
        ]);
    }
}
