<?php

namespace Tests\Feature;

use App\Poll;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class VoteInPollTest extends TestCase
{
    /** @var \App\Poll */
    protected $poll;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->poll = create(Poll::class);
    }

    /** @test */
    public function testGuestsCannotViewPoll()
    {
        Auth::logout();

        $this->getPoll()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUsersCanViewPoll()
    {
        $this->getPoll()
            ->assertOk()
            ->assertJson($this->poll->makeHidden(['thread'])->toArray());
    }

    /** @test */
    public function testPollResponseIncludesUsersVote()
    {
        $this->getPoll()
            ->assertJson(['vote' => []]);

        $this->poll->castVote([$this->poll->options[0]->id]);

        $responseVote = $this->getPoll()->json('vote');

        $this->assertCount(1, $responseVote);
        $this->assertEquals($this->poll->options[0]->id, $responseVote[0]['id']);
    }

    /** @test */
    public function testGuestsCannotVote()
    {
        Auth::logout();

        $this->voteInPoll()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUsersWithAccessToTheThreadCanVote()
    {
        $this->voteInPoll()
            ->assertCreated();

        $this->assertDatabaseCount('poll_votes', 1);
    }

    /** @test */
    public function testUserCanOnlyVoteForAGivenOptionOnce()
    {
        $this->poll->update(['votes_editable' => true]);

        $selectedOption = $this->poll->options[0]->id;

        $this->voteInPoll(['vote' => [$selectedOption]])
            ->assertCreated();

        $this->assertDatabaseCount('poll_votes', 1);

        $this->voteInPoll(['vote' => [$selectedOption]])
            ->assertCreated();

        $this->assertDatabaseCount('poll_votes', 1);
    }

    /** @test */
    public function testUsersCannotVoteOncePollIsLocked()
    {
        $this->poll->update(['locked_at' => now()->subDay()]);

        $this->voteInPoll()
            ->assertForbidden();
    }

    /** @test */
    public function testUsersCannotChangeTheirVoteIfNotAllowed()
    {
        $this->poll->update(['votes_editable' => false]);

        $pollOptions = $this->poll->options->pluck('id');

        // Initial vote.
        $this->voteInPoll(['vote' => [$pollOptions[0]]])
            ->assertCreated();

        $this->assertVoteIs([$pollOptions[0]]);

        // Vote change attempt.
        $this->voteInPoll(['vote' => [$pollOptions[1]]])
            ->assertForbidden();

        $this->assertVoteIs([$pollOptions[0]]);
    }

    /** @test */
    public function testUsersCanChangeTheirVoteIfAllowed()
    {
        $this->poll->update(['votes_editable' => true]);

        $pollOptions = $this->poll->options->pluck('id');

        // Initial vote.
        $this->voteInPoll(['vote' => [$pollOptions[0]]])
            ->assertCreated();

        $this->assertVoteIs([$pollOptions[0]]);

        // Vote change attempt.
        $this->voteInPoll(['vote' => [$pollOptions[1]]])
            ->assertCreated();

        $this->assertVoteIs([$pollOptions[1]]);
    }

    /** @test */
    public function testUsersCanRemoveTheirVoteIfAllowed()
    {
        $this->poll->update(['votes_editable' => true]);

        $pollOptions = $this->poll->options->pluck('id');

        // Initial vote.
        $this->voteInPoll(['vote' => [$pollOptions[0]]])
            ->assertCreated();

        $this->assertVoteIs([$pollOptions[0]]);

        // Vote removal attempt.
        $this->voteInPoll(['vote' => []])
            ->assertCreated();

        $this->assertVoteIs([]);
    }

    /** @test */
    public function testUsersCannotVoteForMoreOptionsThanAllowed()
    {
        $this->poll->update(['max_votes' => 1]);

        $pollOptions = $this->poll->options->pluck('id');

        $this->voteInPoll(['vote' => [$pollOptions[0], $pollOptions[1]]])
            ->assertJsonValidationErrors('vote');

        $this->voteInPoll(['vote' => [$pollOptions[0]]])
            ->assertJsonMissingValidationErrors('vote');
    }

    /**
     * Retrieve poll info.
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function getPoll()
    {
        return $this->getJson(route(
            'polls.show',
            [$this->poll->thread->channel, $this->poll->thread]
        ));
    }

    /**
     * Submit a vote.
     *
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function voteInPoll(array $data = []): \Illuminate\Testing\TestResponse
    {
        $initialData = [
            'vote' => Arr::random(
                $this->poll->options->pluck('id')->all(),
                rand(1, $this->poll->max_votes),
            ),
        ];

        return $this->postJson(
            route('poll_votes.store', $this->poll),
            array_merge($initialData, $data),
        );
    }

    /**
     * Assert that the vote matches the expected value.
     *
     * @param  array  $vote
     */
    protected function assertVoteIs(array $vote): void
    {
        $this->assertEquals(
            $vote, $this->poll->fresh()->getVote()->pluck('option_id')->all(),
            'Vote doesn\'t match expected value.'
        );
    }
}
