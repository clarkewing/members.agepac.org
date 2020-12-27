<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function testGuestsCannotLockThreads()
    {
        Auth::logout();

        $this->lockThread($this->thread)
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotLockThreads()
    {
        $this->signInUnsubscribed();

        $this->lockThread($this->thread)
            ->assertPaymentRequired();
    }

    /** @test */
    public function testUnauthorizedUsersCannotLockThreads()
    {
        $this->lockThread($this->thread)
            ->assertForbidden();

        $this->assertFalse($this->thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }

    /** @test */
    public function testAuthorizedUsersCanLockThreads()
    {
        $this->signInWithPermission('threads.lock');

        $this->lockThread($this->thread)
            ->assertNoContent();

        $this->assertTrue($this->thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /** @test */
    public function testUnauthorizedUsersCannotUnlockThreads()
    {
        $this->thread->update(['locked' => true]);

        $this->unlockThread($this->thread)
            ->assertForbidden();

        $this->assertTrue($this->thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /** @test */
    public function testAuthorizedUsersCanUnlockThreads()
    {
        $this->signInWithPermission('threads.unlock');

        $this->thread->update(['locked' => true]);

        $this->unlockThread($this->thread)
            ->assertNoContent();

        $this->assertFalse($this->thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }

    /** @test */
    public function testALockedThreadMayNotReceiveNewPosts()
    {
        $this->thread->update(['locked' => true]);

        $this->post($this->thread->path() . '/posts', [
            'body' => 'Foobar',
            'user_id' => Auth::id(),
        ])->assertStatus(422);
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function lockThread(Thread $thread): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('threads.lock', $thread));
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function unlockThread(Thread $thread): \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('threads.unlock', $thread));
    }
}
