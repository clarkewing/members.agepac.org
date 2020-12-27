<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Thread;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreatePollTest extends TestCase
{
    /** @var \App\Models\Thread */
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create();

        $this->withExceptionHandling()->signIn($this->thread->creator);
    }

    /** @test */
    public function testGuestsCannotAttachPollToThread()
    {
        Auth::logout();

        $this->attachPollToThread()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotAttachPollToThread()
    {
        $this->signInUnsubscribed();

        $this->attachPollToThread()
            ->assertPaymentRequired();
    }

    /** @test */
    public function testOnlyThreadCreatorCanAttachPollToThread()
    {
        $this->attachPollToThread()
            ->assertCreated();

        Auth::logout();
        $this->signIn();

        $this->attachPollToThread()
            ->assertForbidden();
    }

    /** @test */
    public function testUserWithPermissionCanAttachPollToThread()
    {
        Auth::logout();
        $this->signInWithPermission('threads.edit');

        $this->attachPollToThread()
            ->assertCreated();
    }

    /** @test */
    public function testPollMustHaveAtLeastTwoOptions()
    {
        $this->attachPollToThread(['options' => null])
            ->assertJsonValidationErrors('options');

        $this->attachPollToThread(['options' => [
            ['label' => 'Option 1'],
        ]])
            ->assertJsonValidationErrors('options');

        $this->attachPollToThread(['options' => [
            ['label' => 'Option 1'],
            ['label' => 'Option 2'],
        ]])
            ->assertJsonMissingValidationErrors('options');
    }

    /** @test */
    public function testPollOptionLabelIsRequired()
    {
        $this->attachPollToThread(['options' => [
            ['label' => null],
        ]])
            ->assertJsonValidationErrors('options.0.label');
    }

    /** @test */
    public function testPollOptionLabelCannotBeLongerThan255Characters()
    {
        $this->attachPollToThread(['options' => [
            ['label' => str_repeat('*', 256)],
        ]])
            ->assertJsonValidationErrors('options.0.label');
    }

    /** @test */
    public function testPollOptionColorIsOptional()
    {
        $this->attachPollToThread(['options' => [
            ['label' => 'Cool label', 'color' => null],
        ]])
            ->assertJsonMissingValidationErrors('options.0.color');
    }

    /** @test */
    public function testPollOptionColorMustBeHexColor()
    {
        $this->attachPollToThread(['options' => [
            ['label' => 'Cool label', 'color' => 'foobar'],
        ]])
            ->assertJsonValidationErrors('options.0.color');

        $this->attachPollToThread(['options' => [
            ['label' => 'Cool label', 'color' => '#ffffff'],
        ]])
            ->assertJsonMissingValidationErrors('options.0.color');
    }

    /** @test */
    public function testPollOptionColorCanBe3CharHexColor()
    {
        $this->attachPollToThread(['options' => [
            ['label' => 'Cool label', 'color' => '#fff'],
        ]])
            ->assertJsonMissingValidationErrors('options.0.color');
    }

    /** @test */
    public function testVotesEditableIsRequired()
    {
        $this->attachPollToThread(['votes_editable' => null])
            ->assertJsonValidationErrors('votes_editable');
    }

    /** @test */
    public function testVotesEditableMustBeBoolean()
    {
        $this->attachPollToThread(['votes_editable' => 'foobar'])
            ->assertJsonValidationErrors('votes_editable');
    }

    /** @test */
    public function testMaxVotesIsOptional()
    {
        $this->attachPollToThread(['max_votes' => null])
            ->assertJsonMissingValidationErrors('max_votes');
    }

    /** @test */
    public function testMaxVotesMustBeInteger()
    {
        $this->attachPollToThread(['max_votes' => 'foobar'])
            ->assertJsonValidationErrors('max_votes');
    }

    /** @test */
    public function testMaxVotesMustBeGreaterThan1()
    {
        $this->attachPollToThread(['max_votes' => 0])
            ->assertJsonValidationErrors('max_votes');
    }

    /** @test */
    public function testMaxVotesMustBeLessOrEqualToOptionsCount()
    {
        $this->attachPollToThread([
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
        $this->attachPollToThread(['votes_privacy' => null])
            ->assertJsonValidationErrors('votes_privacy');
    }

    /** @test */
    public function testVotesPrivacyMustBeValidValue()
    {
        $this->attachPollToThread(['votes_privacy' => 'foobar'])
            ->assertJsonValidationErrors('votes_privacy');

        $this->attachPollToThread(['votes_privacy' => 1])
            ->assertJsonValidationErrors('votes_privacy');

        $this->attachPollToThread(['votes_privacy' => 'anonymous'])
            ->assertJsonMissingValidationErrors('votes_privacy');

        $this->attachPollToThread(['votes_privacy' => 'private'])
            ->assertJsonMissingValidationErrors('votes_privacy');

        $this->attachPollToThread(['votes_privacy' => 'public'])
            ->assertJsonMissingValidationErrors('votes_privacy');
    }

    /** @test */
    public function testResultsBeforeVotingIsRequired()
    {
        $this->attachPollToThread(['results_before_voting' => null])
            ->assertJsonValidationErrors('results_before_voting');
    }

    /** @test */
    public function testResultsBeforeVotingMustBeBoolean()
    {
        $this->attachPollToThread(['results_before_voting' => 'foo'])
            ->assertJsonValidationErrors('results_before_voting');
    }

    /** @test */
    public function testLockedAtIsOptional()
    {
        $this->attachPollToThread(['locked_at' => null])
            ->assertJsonMissingValidationErrors('locked_at');
    }

    /** @test */
    public function testLockedAtMustBeDateOfProperFormat()
    {
        $this->attachPollToThread(['locked_at' => 'foobar'])
            ->assertJsonValidationErrors('locked_at');

        $this->attachPollToThread(['locked_at' => 1603356876])
            ->assertJsonValidationErrors('locked_at');

        $this->attachPollToThread(['locked_at' => '2020-09-22 05:00:00'])
            ->assertJsonMissingValidationErrors('locked_at');
    }

    /**
     * Send post request to attach poll to thread.
     *
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function attachPollToThread(array $data = [])
    {
        return $this->postJson(
            route('polls.store', ['channel' => $this->thread->channel, 'thread' => $this->thread]),
            array_merge(
                Arr::except(Poll::factory()->raw(), 'thread_id'),
                ['options' => PollOption::factory()->count(rand(2, 10))->raw(['poll_id' => null])],
                $data,
            )
        );
    }

    /**
     * Send get request to get poll create page.
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function getCreatePollPage()
    {
        return $this->get(route(
            'polls.create',
            [$this->thread->channel, $this->thread]
        ));
    }
}
