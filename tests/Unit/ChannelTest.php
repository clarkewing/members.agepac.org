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
        $fooChannels = create(Channel::class, ['parent_id' => create(Channel::class, ['name' => 'Foo'])->id], 1);
        $barChannels = create(Channel::class, ['parent_id' => create(Channel::class, ['name' => 'Bar'])->id], 2);
        $bazChannels = create(Channel::class, ['parent_id' => create(Channel::class, ['name' => 'Baz'])->id], 3);

        // We expect 4 groups because the parent channels' parent is null.
        $this->assertCount(4, Channel::all()->groupBy('parent.name'));
    }

    /** @test */
    public function testWhenParentIsDeletedChildParentIdIsSetToNull()
    {
        $parentChannel = create(Channel::class);
        $childChannel = create(Channel::class, ['parent_id' => $parentChannel->id]);

        $this->assertEquals($parentChannel->id, $childChannel->parent_id);

        $parentChannel->delete();

        $this->assertNull($childChannel->fresh()->parent_id);
    }
}
