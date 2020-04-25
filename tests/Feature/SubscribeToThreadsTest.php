<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{
    /**
     * @test
     */
    public function testUserCanSubscribeToAThread()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $this->post($thread->path() . '/subscriptions');

        $this->assertCount(1, $thread->subscriptions);
    }

    /**
     * @test
     */
    public function testUserCanUnsubscribeFromAThread()
    {
        $this->signIn();

        $thread = create(Thread::class);
        $thread->subscribe();

        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0, $thread->subscriptions);
    }
}
