<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PinThreadsTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function testGuestsCannotPinThreads()
    {
        Auth::logout();

        $this->pinThread($this->thread)
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotPinThreads()
    {
        $this->signInUnsubscribed();

        $this->pinThread($this->thread)
            ->assertPaymentRequired();
    }

    /** @test */
    public function testUnauthorizedUsersCannotPinThreads()
    {
        $this->pinThread($this->thread)
            ->assertForbidden();

        $this->assertFalse($this->thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /** @test */
    public function testAuthorizedUsersCanPinThreads()
    {
        $this->signInWithPermission('threads.pin');

        $this->pinThread($this->thread)
            ->assertNoContent();

        $this->assertTrue($this->thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function testGuestsCannotUnpinThreads()
    {
        Auth::logout();

        $this->unpinThread($this->thread)
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotUnpinThreads()
    {
        $this->signInUnsubscribed();

        $this->unpinThread($this->thread)
            ->assertPaymentRequired();
    }

    /** @test */
    public function testUnauthorizedUsersCannotUnpinThreads()
    {
        $this->thread->update(['pinned' => true]);

        $this->unpinThread($this->thread)
            ->assertForbidden();

        $this->assertTrue($this->thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function testAuthorizedUsersCanUnpinThreads()
    {
        $this->thread->update(['pinned' => true]);

        $this->signInWithPermission('threads.unpin');

        $this->unpinThread($this->thread)
            ->assertNoContent();

        $this->assertFalse($this->thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /** @test */
    public function testPinnedThreadsAreListedFirst()
    {
        $this->signInWithPermission('threads.pin');

        $threadOne = $this->thread;
        [$threadTwo, $threadThree] = Thread::factory()->count(2)->create();

        $this->getJson(route('threads.index'))->assertJson([
            'data' => [
                ['id' => $threadOne->id],
                ['id' => $threadTwo->id],
                ['id' => $threadThree->id],
            ],
        ]);

        $this->pinThread($pinnedThread = $threadThree)
            ->assertNoContent();

        $this->getJson(route('threads.index'))->assertJson([
            'data' => [
                ['id' => $pinnedThread->id],
                ['id' => $threadOne->id],
                ['id' => $threadTwo->id],
            ],
        ]);
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function pinThread(Thread $thread): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('threads.pin', $thread));
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function unpinThread(Thread $thread): \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('threads.unpin', $thread));
    }
}
