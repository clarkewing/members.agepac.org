<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class LockThreadsTest extends TestCase
{
    /**
     * @test
     */
    public function testNonAdministratorsCannotLockThreads()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create(Thread::class);

        $this->post(route('locked-threads.store', $thread))
            ->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    }

    /**
     * @test
     */
    public function testNonAdministratorsCannotUnlockThreads()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create(Thread::class, ['locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread))
            ->assertStatus(403);

        $this->assertTrue($thread->fresh()->locked);
    }

    /**
     * @test
     */
    public function testAdministratorsCanLockThreads()
    {
        $this->signIn(factory(User::class)->states('administrator')->create());

        $thread = create(Thread::class);

        $this->post(route('locked-threads.store', $thread))
            ->assertStatus(204);

        $this->assertTrue($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /**
     * @test
     */
    public function testAdministratorsCanUnlockThreads()
    {
        $this->signIn(factory(User::class)->states('administrator')->create());

        $thread = create(Thread::class, ['locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread))
            ->assertStatus(204);

        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }

    /**
     * @test
     */
    public function testALockedThreadMayNotReceiveNewReplies()
    {
        $this->signIn();

        $thread = create(Thread::class, ['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => Auth::id(),
        ])->assertStatus(422);
    }
}
