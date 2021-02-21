<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
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
    public function testGuestsCannotToggleThreadPin()
    {
        Auth::logout();

        $this->toggleThreadPin()
            ->assertForbidden();
    }

    /** @test */
    public function testUnsubscribedUsersCannotToggleThreadPin()
    {
        $this->signInUnsubscribed();

        $this->toggleThreadPin()
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotPinThreads()
    {
        $this->toggleThreadPin()
            ->assertForbidden();

        $this->assertFalse($this->thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /** @test */
    public function testAuthorizedUsersCanPinThreads()
    {
        $this->signInWithPermission('threads.pin');

        $this->toggleThreadPin()
            ->assertOk();

        $this->assertTrue($this->thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function testUnauthorizedUsersCannotUnpinThreads()
    {
        $this->thread->update(['pinned' => true]);

        $this->toggleThreadPin()
            ->assertForbidden();

        $this->assertTrue($this->thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function testAuthorizedUsersCanUnpinThreads()
    {
        $this->signInWithPermission('threads.unpin');

        $this->thread->update(['pinned' => true]);

        $this->toggleThreadPin()
            ->assertOk();

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

        Livewire::test(\App\Http\Livewire\Thread::class, [$pinnedThread = $threadThree])
            ->call('togglePin')
            ->assertOk();

        $this->getJson(route('threads.index'))->assertJson([
            'data' => [
                ['id' => $pinnedThread->id],
                ['id' => $threadOne->id],
                ['id' => $threadTwo->id],
            ],
        ]);
    }

    /**
     * @return \Livewire\Testing\TestableLivewire
     */
    protected function toggleThreadPin(): \Livewire\Testing\TestableLivewire
    {
        return Livewire::test(\App\Http\Livewire\Thread::class, [$this->thread])
            ->call('togglePin');
    }
}
