<?php

namespace Tests\Unit;

use App\Channel;
use App\Thread;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    /**
     * @test
     */
    public function testConsistsOfThreads()
    {
        $channel = create(Channel::class);
        $thread = create(Thread::class, ['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }
}
