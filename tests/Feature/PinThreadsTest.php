<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;

class PinThreadsTest extends TestCase
{
    /** @test */
    public function testNonAdministratorsCannotPinThreads()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create(Thread::class);

        $this->post(route('threads.pin', $thread))
            ->assertStatus(403);

        $this->assertFalse($thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /** @test */
    public function testNonAdministratorsCannotUnpinThreads()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create(Thread::class, ['pinned' => true]);

        $this->delete(route('threads.unpin', $thread))
            ->assertStatus(403);

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function testAdministratorsCanPinThreads()
    {
        $this->signInGod();

        $thread = create(Thread::class);

        $this->post(route('threads.pin', $thread))
            ->assertStatus(204);

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function testAdministratorsCanUnpinThreads()
    {
        $this->signInGod();

        $thread = create(Thread::class, ['pinned' => true]);

        $this->delete(route('threads.unpin', $thread))
            ->assertStatus(204);

        $this->assertFalse($thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /** @test */
    public function testPinnedThreadsAreListedFirst()
    {
        $this->signInGod();

        [$threadOne, $threadTwo, $threadThree] = create(Thread::class, [], 3);

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
