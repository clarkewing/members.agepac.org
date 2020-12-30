<?php

namespace Tests\Feature\Nova;

use App\Models\Channel;
use App\Models\Thread;
use Tests\NovaTestRequests;
use Tests\TestCase;

class UpdateThreadChannelActionTest extends TestCase
{
    use NovaTestRequests, MergesModels;

    protected $threads;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signInWithRole('Administrator');

        $this->threads = Thread::factory()->count(2)->create();
    }

    /** @test */
    public function testChannelIsUpdatedForSelectedModels()
    {
        $desiredChannel = Channel::factory()->create();

        $this->assertNotContains($desiredChannel->id, $this->threads->pluck('id'));

        $this->updateChannel($this->threads->pluck('id')->all(), $desiredChannel)
            ->assertJson(['message' => 'Channel successfully updated for selected threads.']);

        $this->assertEquals($desiredChannel->id, $this->threads[0]->fresh()->channel_id);
        $this->assertEquals($desiredChannel->id, $this->threads[1]->fresh()->channel_id);
    }

    /** @test */
    public function testChannelIsRequired()
    {
        $this->updateChannel($this->threads->pluck('id')->all(), null)
            ->assertJsonValidationErrors('channel_id');
    }

    /** @test */
    public function testChannelMustExist()
    {
        $this->updateChannel($this->threads->pluck('id')->all(), 99)
            ->assertJsonValidationErrors('channel_id');
    }

    /**
     * @param $resourceIds
     * @param $channel
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateChannel(array $resourceIds, $channel): \Illuminate\Testing\TestResponse
    {
        return $this->performResourceAction('threads', 'update-channel', [
            'resources' => implode(',', $resourceIds),
            'channel_id' => $channel instanceof Channel ? $channel->id : $channel,
        ]);
    }
}
