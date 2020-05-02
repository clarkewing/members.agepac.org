<?php

namespace Tests\Unit;

use App\Channel;
use App\Thread;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    /** @test */
    public function testConsistsOfThreads()
    {
        $channel = create(Channel::class);
        $thread = create(Thread::class, ['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }

    /** @test */
    public function testCanBeArchived()
    {
        $channel = create(Channel::class);

        $this->assertFalse($channel->archived);

        $channel->archive();

        $this->assertTrue($channel->archived);
    }

    /** @test */
    public function testCanBeUnarchived()
    {
        $channel = create(Channel::class, ['archived' => true]);

        $this->assertTrue($channel->archived);

        $channel->unarchive();

        $this->assertFalse($channel->archived);
    }
}
