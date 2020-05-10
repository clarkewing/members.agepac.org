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

    /** @test */
    public function testArchivedChannelsAreExcludedByDefault()
    {
        create(Channel::class);
        create(Channel::class, ['archived' => true]);

        $this->assertEquals(1, Channel::count());
    }

    /** @test */
    public function testSortedAlphabeticallyByDefault()
    {
        $channelOne = create(Channel::class, ['name' => 'Brontosaurus']);
        $channelTwo = create(Channel::class, ['name' => 'Antelope']);
        $channelThree = create(Channel::class, ['name' => 'Zebra']);

        $this->assertEquals(
            [
                $channelTwo->id,
                $channelOne->id,
                $channelThree->id,
            ],
            Channel::pluck('id')->all()
        );
    }

    /** @test */
    public function testCanGroupByParent()
    {
        $fooChannels = create(Channel::class, ['parent' => 'Foo'], 1);
        $barChannels = create(Channel::class, ['parent' => 'Bar'], 2);
        $bazChannels = create(Channel::class, ['parent' => 'Baz'], 3);

        $this->assertCount(3, Channel::all()->groupBy('parent'));
    }
}
