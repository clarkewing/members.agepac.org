<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
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
    public function testGuestsCannotToggleThreadLock()
    {
        Auth::logout();

        $this->toggleThreadLock()
            ->assertForbidden();
    }

    /** @test */
    public function testUnsubscribedUsersCannotToggleThreadLock()
    {
        $this->signInUnsubscribed();

        $this->toggleThreadLock()
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotLockThreads()
    {
        $this->toggleThreadLock()
            ->assertForbidden();

        $this->assertFalse($this->thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }

    /** @test */
    public function testAuthorizedUsersCanLockThreads()
    {
        $this->signInWithPermission('threads.lock');

        $this->toggleThreadLock()
            ->assertOk();

        $this->assertTrue($this->thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /** @test */
    public function testUnauthorizedUsersCannotUnlockThreads()
    {
        $this->thread->update(['locked' => true]);

        $this->toggleThreadLock()
            ->assertForbidden();

        $this->assertTrue($this->thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /** @test */
    public function testAuthorizedUsersCanUnlockThreads()
    {
        $this->signInWithPermission('threads.unlock');

        $this->thread->update(['locked' => true]);

        $this->toggleThreadLock()
            ->assertOk();

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
     * @return \Livewire\Testing\TestableLivewire
     */
    protected function toggleThreadLock(): \Livewire\Testing\TestableLivewire
    {
        return Livewire::test(\App\Http\Livewire\Thread::class, [$this->thread])
            ->call('toggleLock');
    }
}
