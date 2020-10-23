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
        $poll = create(Poll::class, ['votes_privacy' => 'public']);
        $this->assertEquals(0, $poll->fresh()->votes_privacy);

        $poll = create(Poll::class, ['votes_privacy' => 'private']);
        $this->assertEquals(1, $poll->fresh()->votes_privacy);

        $poll = create(Poll::class, ['votes_privacy' => 'anonymous']);
        $this->assertEquals(2, $poll->fresh()->votes_privacy);
    }

    /** @test */
    public function testCanAddOptionToPoll()
    {
        $poll = create(Poll::class);

        $optionsCount = $poll->options()->count();

        $poll->addOption(make(PollOption::class)->toArray());

        $this->assertEquals($optionsCount + 1, $poll->options()->count());
    }

    /** @test */
    public function testCanAddMultipleOptionsToPoll()
    {
        $poll = create(Poll::class);

        $optionsCount = $poll->options()->count();

        $poll->addOptions(make(PollOption::class, [], 3)->toArray());

        $this->assertEquals($optionsCount + 3, $poll->options()->count());
    }

    /** @test */
    public function testCanAddNoOptionsToPoll()
    {
        $poll = create(Poll::class);

        $optionsCount = $poll->options()->count();

        $poll->addOptions(null);

        $this->assertEquals($optionsCount, $poll->options()->count());

        $poll->addOptions([]);

        $this->assertEquals($optionsCount, $poll->options()->count());
    }

    /** @test */
    public function testCanSyncPollOptions()
    {
        $poll = create(Poll::class);

        $poll->syncOptions(null);

        $this->assertEquals(0, $poll->options()->count());

        $poll->syncOptions([]);

        $this->assertEquals(0, $poll->options()->count());

        $poll->syncOptions([make(PollOption::class)->toArray()]);

        $this->assertEquals(1, $poll->options()->count());

        $poll->syncOptions(make(PollOption::class, [], 3)->toArray());

        $this->assertEquals(3, $poll->options()->count());
    }

    /** @test */
    public function testReturnsFormattedResults()
    {
        ($poll = create(Poll::class))
            ->syncOptions(make(PollOption::class, [], 2)->toArray());

        $this->assertEquals(0, $poll->getResults()[0]->votes_count);
        $this->assertEquals(0, $poll->getResults()[0]->votes_percent);
        $this->assertEquals(0, $poll->getResults()[1]->votes_count);
        $this->assertEquals(0, $poll->getResults()[1]->votes_percent);

        $poll->castVote([$poll->options[0]->id], create(User::class));

        $this->assertEquals(1, $poll->getResults()[0]->votes_count);
        $this->assertEquals(100, $poll->getResults()[0]->votes_percent);
        $this->assertEquals(0, $poll->getResults()[1]->votes_count);
        $this->assertEquals(0, $poll->getResults()[1]->votes_percent);

        $poll->castVote([$poll->options[1]->id], create(User::class));

        $this->assertEquals(1, $poll->getResults()[0]->votes_count);
        $this->assertEquals(50, $poll->getResults()[0]->votes_percent);
        $this->assertEquals(1, $poll->getResults()[1]->votes_count);
        $this->assertEquals(50, $poll->getResults()[1]->votes_percent);
    }

    /** @test */
    public function testResultsPercentagesAreRoundedTo3DecimalPoints()
    {
        ($poll = create(Poll::class))
            ->syncOptions(make(PollOption::class, [], 2)->toArray());

        $poll->castVote([$poll->options[0]->id], create(User::class));
        $poll->castVote([$poll->options[0]->id], create(User::class));
        $poll->castVote([$poll->options[1]->id], create(User::class));

        $this->assertEquals(66.667, $poll->getResults()[0]->votes_percent);
        $this->assertEquals(33.333, $poll->getResults()[1]->votes_percent);
    }

    /** @test */
    public function testResultsCanIncludeVoters()
    {
        $poll = create(Poll::class);

        $poll->castVote([$poll->options[0]->id], $voter = create(User::class));

        $this->assertTrue($voter->is($poll->getResults()[0]->voters[0]));
    }
}
