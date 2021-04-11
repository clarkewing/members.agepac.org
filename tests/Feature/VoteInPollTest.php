<?php

namespace Tests\Feature;

use App\Models\Poll;
use Illuminate\Support\Arr;
use Livewire\Livewire;
use Tests\TestCase;

class VoteInPollTest extends TestCase
{
    /** @var \App\Models\Poll */
    protected $poll;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->poll = Poll::factory()->create();
    }

    /** @test */
    public function testPollIsRendered()
    {
        $this->get($this->poll->thread->path())
            ->assertSeeLivewire('thread-poll');
    }

    /** @test */
    public function testUsersCanVote()
    {
        $this->voteInPoll()
            ->assertHasNoErrors();

        $this->assertDatabaseCount('poll_votes', 1);
    }

    /** @test */
    public function testUserIsShownResultsAfterVoting()
    {
        $this->voteInPoll()
            ->assertHasNoErrors()
            ->assertSet('panel', 'results');
    }

    /** @test */
    public function testUserCanOnlyVoteForAGivenOptionOnce()
    {
        $this->poll->update(['votes_editable' => true]);

        $selectedOption = $this->poll->options[0]->id;

        $this->voteInPoll([$selectedOption])
            ->assertHasNoErrors();

        $this->assertDatabaseCount('poll_votes', 1);

        $this->voteInPoll([$selectedOption])
            ->assertHasNoErrors();

        $this->assertDatabaseCount('poll_votes', 1);
    }

    /** @test */
    public function testUsersCannotVoteOncePollIsLocked()
    {
        $this->poll->update(['locked_at' => now()->subDay()]);

        $this->viewBallot()
            ->assertSee('Tu ne peux pas modifier ton vote');

        $this->voteInPoll()
            ->assertForbidden();
    }

    /** @test */
    public function testUsersCannotChangeTheirVoteIfNotAllowed()
    {
        $this->poll->update(['votes_editable' => false]);

        $pollOptions = $this->poll->options->pluck('id');

        // Initial vote.
        $this->viewBallot()
            ->assertSee('Soumettre');
        $this->voteInPoll([$pollOptions[0]])
            ->assertHasNoErrors();

        $this->assertVoteIs([$pollOptions[0]]);

        // Vote change attempt.
        $this->viewBallot()
            ->assertSee('Tu ne peux pas modifier ton vote');
        $this->voteInPoll([$pollOptions[1]])
            ->assertForbidden();

        $this->assertVoteIs([$pollOptions[0]]);
    }

    /** @test */
    public function testUsersCanChangeTheirVoteIfAllowed()
    {
        $this->poll->update(['votes_editable' => true]);

        $pollOptions = $this->poll->options->pluck('id');

        // Initial vote.
        $this->viewBallot()
            ->assertSee('Soumettre');
        $this->voteInPoll([$pollOptions[0]])
            ->assertHasNoErrors();

        $this->assertVoteIs([$pollOptions[0]]);

        // Vote change attempt.
        $this->viewBallot()
            ->assertSee('Soumettre');
        $this->voteInPoll([$pollOptions[1]])
            ->assertHasNoErrors();

        $this->assertVoteIs([$pollOptions[1]]);
    }

    /** @test */
    public function testUsersCanRemoveTheirVoteIfAllowed()
    {
        $this->poll->update(['votes_editable' => true]);

        $pollOptions = $this->poll->options->pluck('id');

        // Initial vote.
        $this->voteInPoll([$pollOptions[0]])
            ->assertHasNoErrors();

        $this->assertVoteIs([$pollOptions[0]]);

        // Vote removal attempt.
        $this->voteInPoll([])
            ->assertHasNoErrors();

        $this->assertVoteIs([]);
    }

    /** @test */
    public function testUsersCannotVoteForMoreOptionsThanAllowed()
    {
        $this->poll->update(['max_votes' => 1]);

        $pollOptions = $this->poll->options->pluck('id');

        $this->voteInPoll([$pollOptions[0], $pollOptions[1]])
            ->assertHasErrors(['state.vote' => 'max']);

        $this->voteInPoll([$pollOptions[0]])
            ->assertHasNoErrors(['state.vote' => 'max']);
    }

    /** @test */
    public function testUsersCanVoteForUnlimitedOptionsIfMaxVotesNull()
    {
        $this->poll->update(['max_votes' => null]);

        $pollOptions = $this->poll->options->pluck('id');

        $this->voteInPoll([$pollOptions[0], $pollOptions[1]])
            ->assertHasNoErrors(['state.vote' => 'max']);
    }

    /** @test */
    public function testVotedOptionMustBeAssociatedWithPoll()
    {
        $this->voteInPoll([99])
            ->assertHasErrors(['state.vote.0' => 'in']);
    }

    protected function viewBallot(): \Livewire\Testing\TestableLivewire
    {
        return Livewire::test('thread-poll', ['thread' => $this->poll->thread])
            ->call('showBallot');
    }

    /**
     * Submit a vote.
     *
     * @param  array|null  $vote
     * @return \Livewire\Testing\TestableLivewire
     */
    protected function voteInPoll(array $vote = null): \Livewire\Testing\TestableLivewire
    {
        if (is_null($vote)) {
            $vote = Arr::random(
                $this->poll->options->pluck('id')->all(),
                rand(1, $this->poll->max_votes),
            );
        }

        return $this->viewBallot()
            ->set('state.vote', $vote)
            ->call('castVote');
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
