<?php

namespace Tests\Feature;

use App\Models\Thread;
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
    public function testGuestsCannotSubscribeToAThread()
    {
        $this->storeSubscription($this->thread)
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotSubscribeToAThread()
    {
        $this->signInUnsubscribed();

        $this->storeSubscription($this->thread)
            ->assertPaymentRequired();
    }

    /** @test */
    public function testSubscribedUsersCanSubscribeToAThread()
    {
        $this->signIn();

        $this->storeSubscription($this->thread);

        $this->assertCount(1, $this->thread->subscriptions);
    }

    /** @test */
    public function testGuestsCannotUnsubscribeFromAThread()
    {
        $this->deleteSubscription($this->thread)
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotUnsubscribeFromAThread()
    {
        $this->signInUnsubscribed();

        $this->deleteSubscription($this->thread)
            ->assertPaymentRequired();
    }

    /** @test */
    public function testSubscribedUsersCanUnsubscribeFromAThread()
    {
        $this->signIn();

        $this->thread->subscribe();

        $this->deleteSubscription($this->thread);

        $this->assertCount(0, $this->thread->subscriptions);
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storeSubscription(Thread $thread): \Illuminate\Testing\TestResponse
    {
        return $this->postJson($thread->path() . '/subscriptions');
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deleteSubscription(Thread $thread): \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson($thread->path() . '/subscriptions');
    }
}
