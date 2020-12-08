<?php

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Thread;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    /** @test */
    public function testConsistsOfThreads()
    {
        $channel = Channel::factory()->create();
        $thread = Thread::factory()->create(['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }

    /** @test */
    public function testCanBeArchived()
    {
        $channel = Channel::factory()->create();

        $this->assertFalse($channel->archived);

        $channel->archive();

        $this->assertTrue($channel->archived);
    }

    /** @test */
    public function testCanBeUnarchived()
    {
        $channel = Channel::factory()->create(['archived' => true]);

        $this->assertTrue($channel->archived);

        $channel->unarchive();

        $this->assertFalse($channel->archived);
    }

    /** @test */
    public function testArchivedChannelsAreExcludedByDefault()
    {
        Channel::factory()->create();
        Channel::factory()->create(['archived' => true]);

        $this->assertEquals(1, Channel::count());
    }

    /** @test */
    public function testSortedAlphabeticallyByDefault()
    {
        $channelOne = Channel::factory()->create(['name' => 'Brontosaurus']);
        $channelTwo = Channel::factory()->create(['name' => 'Antelope']);
        $channelThree = Channel::factory()->create(['name' => 'Zebra']);

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
        $fooChannels = Channel::factory()->count(1)->create(['parent_id' => Channel::factory()->create(['name' => 'Foo'])->id]);
        $barChannels = Channel::factory()->count(2)->create(['parent_id' => Channel::factory()->create(['name' => 'Bar'])->id]);
        $bazChannels = Channel::factory()->count(3)->create(['parent_id' => Channel::factory()->create(['name' => 'Baz'])->id]);

        // We expect 4 groups because the parent channels' parent is null.
        $this->assertCount(4, Channel::all()->groupBy('parent.name'));
    }

    /** @test */
    public function testWhenParentIsDeletedChildParentIdIsSetToNull()
    {
        $parentChannel = Channel::factory()->create();
        $childChannel = Channel::factory()->create(['parent_id' => $parentChannel->id]);

        $this->assertEquals($parentChannel->id, $childChannel->parent_id);

        $parentChannel->delete();

        $this->assertNull($childChannel->fresh()->parent_id);
    }
}
