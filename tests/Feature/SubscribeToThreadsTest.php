<?php

namespace Tests\Feature;

use App\Models\Thread;
use Livewire\Livewire;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function testGuestsCannotToggleSubscriptionToAThread()
    {
        $this->markTestSkipped('Awaiting ability to test Livewire with middleware.');

        $this->toggleSubscription()
            ->assertForbidden();
    }

    /** @test */
    public function testUnsubscribedUsersCannotToggleSubscriptionToAThread()
    {
        $this->markTestSkipped('Awaiting ability to test Livewire with middleware.');

        $this->signInUnsubscribed();

        $this->toggleSubscription()
            ->assertForbidden();
    }

    /** @test */
    public function testCanSubscribeToAThread()
    {
        $this->signIn();

        $this->toggleSubscription();

        $this->assertCount(1, $this->thread->subscriptions);
    }

    /** @test */
    public function testCanUnsubscribeFromAThread()
    {
        $this->signIn();

        $this->thread->subscribe();

        $this->toggleSubscription();

        $this->assertCount(0, $this->thread->subscriptions);
    }

    protected function toggleSubscription()
    {
        return Livewire::test(\App\Http\Livewire\ThreadHeader::class, [$this->thread])
            ->call('toggleSubscription');
    }
}
