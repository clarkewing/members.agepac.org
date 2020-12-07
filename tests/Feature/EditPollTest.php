<?php

namespace Tests\Feature;

use App\Poll;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EditPollTest extends TestCase
{
    /** @var \App\Poll */
    protected $poll;

    protected function setUp(): void
    {
        parent::setUp();

        $this->poll = create(Poll::class);

        $this->withExceptionHandling()->signIn($this->poll->thread->creator);
    }

    /** @test */
    public function testGuestsCannotEditPoll()
    {
        Auth::logout();

        $this->updatePoll()
            ->assertUnauthorized();
    }

    /** @test */
    public function testOnlyPollThreadCreatorCanEditPoll()
    {
        $this->updatePoll()
            ->assertOk();

        Auth::logout();
        $this->signIn();

        $this->updatePoll()
            ->assertForbidden();
    }

    /** @test */
    public function testUserWithPermissionCanEditPoll()
    {
        Auth::logout();
        $this->signInWithPermission('threads.edit');

        $this->updatePoll()
            ->assertOk();
    }

    /** @test */
    public function testPollCannotBeEditedIfItIsLocked()
    {
        $this->poll->update(['locked_at' => now()]);

        $this->updatePoll()
            ->assertForbidden();
    }

    /** @test */
    public function testPollCannotBeEditedIfItsThreadIsLocked()
    {
        $this->poll->thread->update(['locked' => true]);

        $this->updatePoll()
            ->assertForbidden();
    }

    /** @test */
    public function testPollCannotBeEditedIfItHasReceivedVotes()
    {
        $this->poll->castVote([$this->poll->options[0]->id]);

        $this->updatePoll()
            ->assertForbidden();
    }

    /** @test */
    public function testPollMustHaveAtLeastTwoOptions()
    {
        $this->updatePoll(['options' => null])
            ->assertJsonValidationErrors('options');

        $this->updatePoll(['options' => [
            ['label' => 'Option 1'],
        ]])
            ->assertJsonValidationErrors('options');

        $this->updatePoll(['options' => [
            ['label' => 'Option 1'],
            ['label' => 'Option 2'],
        ]])
            ->assertJsonMissingValidationErrors('options');
    }

    /** @test */
    public function testPollOptionLabelIsRequired()
    {
        $this->updatePoll(['options' => [
            ['label' => null],
        ]])
            ->assertJsonValidationErrors('options.0.label');
    }

    /** @test */
    public function testPollOptionLabelCannotBeLongerThan255Characters()
    {
        $this->updatePoll(['options' => [
            ['label' => str_repeat('*', 256)],
        ]])
            ->assertJsonValidationErrors('options.0.label');
    }

    /** @test */
    public function testPollOptionColorIsOptional()
    {
        $this->updatePoll(['options' => [
            ['label' => 'Cool label', 'color' => null],
        ]])
            ->assertJsonMissingValidationErrors('options.0.color');
    }

    /** @test */
    public function testPollOptionColorMustBeHexColor()
    {
        $this->updatePoll(['options' => [
            ['label' => 'Cool label', 'color' => 'foobar'],
        ]])
            ->assertJsonValidationErrors('options.0.color');

        $this->updatePoll(['options' => [
            ['label' => 'Cool label', 'color' => '#ffffff'],
        ]])
            ->assertJsonMissingValidationErrors('options.0.color');
    }

    /** @test */
    public function testPollOptionColorCanBe3CharHexColor()
    {
        $this->updatePoll(['options' => [
            ['label' => 'Cool label', 'color' => '#fff'],
        ]])
            ->assertJsonMissingValidationErrors('options.0.color');
    }

    /** @test */
    public function testVotesEditableIsRequired()
    {
        $this->updatePoll(['votes_editable' => null])
            ->assertJsonValidationErrors('votes_editable');
    }

    /** @test */
    public function testVotesEditableMustBeBoolean()
    {
        $this->updatePoll(['votes_editable' => 'foobar'])
            ->assertJsonValidationErrors('votes_editable');
    }

    /** @test */
    public function testMaxVotesIsOptional()
    {
        $this->updatePoll(['max_votes' => null])
            ->assertJsonMissingValidationErrors('max_votes');
    }

    /** @test */
    public function testMaxVotesMustBeInteger()
    {
        $this->updatePoll(['max_votes' => 'foobar'])
            ->assertJsonValidationErrors('max_votes');
    }

    /** @test */
    public function testMaxVotesMustBeGreaterThan1()
    {
        $this->updatePoll(['max_votes' => 0])
            ->assertJsonValidationErrors('max_votes');
    }

    /** @test */
    public function testMaxVotesMustBeLessOrEqualToOptionsCount()
    {
        $this->updatePoll([
            'options' => [
                ['label' => 'Option 1'],
                ['label' => 'Option 2'],
            ],
            'max_votes' => 3,
        ])
            ->assertJsonValidationErrors('max_votes');
    }

    /** @test */
    public function testVotesPrivacyIsRequired()
    {
        $this->updatePoll(['votes_privacy' => null])
            ->assertJsonValidationErrors('votes_privacy');
    }

    /** @test */
    public function testVotesPrivacyMustBeValidValue()
    {
        $this->updatePoll(['votes_privacy' => 'foobar'])
            ->assertJsonValidationErrors('votes_privacy');

        $this->updatePoll(['votes_privacy' => 1])
            ->assertJsonValidationErrors('votes_privacy');

        $this->updatePoll(['votes_privacy' => 'anonymous'])
            ->assertJsonMissingValidationErrors('votes_privacy');

        $this->updatePoll(['votes_privacy' => 'private'])
            ->assertJsonMissingValidationErrors('votes_privacy');

        $this->updatePoll(['votes_privacy' => 'public'])
            ->assertJsonMissingValidationErrors('votes_privacy');
    }

    /** @test */
    public function testResultsBeforeVotingIsRequired()
    {
        $this->updatePoll(['results_before_voting' => null])
            ->assertJsonValidationErrors('results_before_voting');
    }

    /** @test */
    public function testResultsBeforeVotingMustBeBoolean()
    {
        $this->updatePoll(['results_before_voting' => 'foo'])
            ->assertJsonValidationErrors('results_before_voting');
    }

    /** @test */
    public function testLockedAtIsOptional()
    {
        $this->updatePoll(['locked_at' => null])
            ->assertJsonMissingValidationErrors('locked_at');
    }

    /** @test */
    public function testLockedAtMustBeDateOfProperFormat()
    {
        $this->updatePoll(['locked_at' => 'foobar'])
            ->assertJsonValidationErrors('locked_at');

        $this->updatePoll(['locked_at' => 1603356876])
            ->assertJsonValidationErrors('locked_at');

        $this->updatePoll(['locked_at' => '2020-09-22 05:00:00'])
            ->assertJsonMissingValidationErrors('locked_at');
    }

    /** @test */
    public function testGuestsCannotDeletePoll()
    {
        Auth::logout();

        $this->deletePoll()
            ->assertUnauthorized();
    }

    /** @test */
    public function testOnlyPollThreadCreatorCanDeletePoll()
    {
        Auth::logout();
        $this->signIn();

        $this->deletePoll()
            ->assertForbidden();

        $this->signIn($this->poll->thread->creator);

        $this->deletePoll()
            ->assertNoContent();
    }

    /** @test */
    public function testUserWithPermissionCanDeletePoll()
    {
        Auth::logout();
        $this->signInWithPermission('threads.edit');

        $this->deletePoll()
            ->assertNoContent();
    }

    /** @test */
    public function testPollCannotBeDeletedIfItsThreadIsLocked()
    {
        $this->poll->thread->update(['locked' => true]);

        $this->deletePoll()
            ->assertForbidden();
    }

    /**
     * Send put request to update the poll.
     *
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updatePoll(array $data = [])
    {
        return $this->putJson(
            route('polls.update', [$this->poll->thread->channel, $this->poll->thread]),
            $data
        );
    }

    /**
     * Send delete request to destroy the poll.
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deletePoll()
    {
        return $this->deleteJson(
            route('polls.destroy', [$this->poll->thread->channel, $this->poll->thread])
        );
    }
}
