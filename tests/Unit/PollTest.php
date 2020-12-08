<?php

namespace Tests\Unit;

use App\Poll;
use App\PollOption;
use App\User;
use Tests\TestCase;

class PollTest extends TestCase
{
    /** @test */
    public function testStringVotesPrivacyIsConvertedToIntValueInDB()
    {
        $poll = Poll::factory()->create(['votes_privacy' => 'public']);
        $this->assertEquals(0, $poll->fresh()->votes_privacy);

        $poll = Poll::factory()->create(['votes_privacy' => 'private']);
        $this->assertEquals(1, $poll->fresh()->votes_privacy);

        $poll = Poll::factory()->create(['votes_privacy' => 'anonymous']);
        $this->assertEquals(2, $poll->fresh()->votes_privacy);
    }

    /** @test */
    public function testCanAddOptionToPoll()
    {
        $poll = Poll::factory()->create();

        $optionsCount = $poll->options()->count();

        $poll->addOption(PollOption::factory()->raw());

        $this->assertEquals($optionsCount + 1, $poll->options()->count());
    }

    /** @test */
    public function testCanAddMultipleOptionsToPoll()
    {
        $poll = Poll::factory()->create();

        $optionsCount = $poll->options()->count();

        $poll->addOptions(PollOption::factory()->count(3)->raw());

        $this->assertEquals($optionsCount + 3, $poll->options()->count());
    }

    /** @test */
    public function testCanAddNoOptionsToPoll()
    {
        $poll = Poll::factory()->create();

        $optionsCount = $poll->options()->count();

        $poll->addOptions(null);

        $this->assertEquals($optionsCount, $poll->options()->count());

        $poll->addOptions([]);

        $this->assertEquals($optionsCount, $poll->options()->count());
    }

    /** @test */
    public function testCanSyncPollOptions()
    {
        $poll = Poll::factory()->create();

        $poll->syncOptions(null);

        $this->assertEquals(0, $poll->options()->count());

        $poll->syncOptions([]);

        $this->assertEquals(0, $poll->options()->count());

        $poll->syncOptions([PollOption::factory()->raw()]);

        $this->assertEquals(1, $poll->options()->count());

        $poll->syncOptions(PollOption::factory()->count(3)->raw());

        $this->assertEquals(3, $poll->options()->count());
    }

    /** @test */
    public function testReturnsFormattedResults()
    {
        ($poll = Poll::factory()->create())
            ->syncOptions(PollOption::factory()->count(2)->raw());

        $this->assertEquals(0, $poll->getResults()[0]->votes_count);
        $this->assertEquals(0, $poll->getResults()[0]->votes_percent);
        $this->assertEquals(0, $poll->getResults()[1]->votes_count);
        $this->assertEquals(0, $poll->getResults()[1]->votes_percent);

        $poll->castVote([$poll->options[0]->id], User::factory()->create());

        $this->assertEquals(1, $poll->getResults()[0]->votes_count);
        $this->assertEquals(100, $poll->getResults()[0]->votes_percent);
        $this->assertEquals(0, $poll->getResults()[1]->votes_count);
        $this->assertEquals(0, $poll->getResults()[1]->votes_percent);

        $poll->castVote([$poll->options[1]->id], User::factory()->create());

        $this->assertEquals(1, $poll->getResults()[0]->votes_count);
        $this->assertEquals(50, $poll->getResults()[0]->votes_percent);
        $this->assertEquals(1, $poll->getResults()[1]->votes_count);
        $this->assertEquals(50, $poll->getResults()[1]->votes_percent);
    }

    /** @test */
    public function testResultsPercentagesAreRoundedTo3DecimalPoints()
    {
        ($poll = Poll::factory()->create())
            ->syncOptions(PollOption::factory()->count(2)->raw());

        $poll->castVote([$poll->options[0]->id], User::factory()->create());
        $poll->castVote([$poll->options[0]->id], User::factory()->create());
        $poll->castVote([$poll->options[1]->id], User::factory()->create());

        $this->assertEquals(66.667, $poll->getResults()[0]->votes_percent);
        $this->assertEquals(33.333, $poll->getResults()[1]->votes_percent);
    }

    /** @test */
    public function testResultsCanIncludeVoters()
    {
        $poll = Poll::factory()->create();

        $poll->castVote([$poll->options[0]->id], $voter = User::factory()->create());

        $this->assertTrue($voter->is($poll->getResults()[0]->voters[0]));
    }

    /** @test */
    public function testKnowsIfVotesWereCast()
    {
        $poll = Poll::factory()->create();

        $this->assertFalse($poll->has_votes);

        $poll->castVote([$poll->options[0]->id], $voter = User::factory()->create());

        $this->assertTrue($poll->has_votes);
    }
}
