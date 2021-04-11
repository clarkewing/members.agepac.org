<?php

namespace Tests\Feature;

use App\Http\Livewire\ThreadPollForm;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Thread;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;
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
    public function testRandomUserCannotSeeButtonToAttachPoll()
    {
        $this->signIn();

        $this->get($this->thread->path())
            ->assertDontSee('Ajouter un Sondage');
    }

    /** @test */
    public function testUserWithPermissionCanSeeButtonToAttachPoll()
    {
        $this->signInWithPermission('threads.edit');

        $this->get($this->thread->path())
            ->assertSee('Ajouter un Sondage');
    }

    /** @test */
    public function testThreadCreatorCanSeeButtonToAttachPoll()
    {
        $this->get($this->thread->path())
            ->assertSee('Ajouter un Sondage');
    }

    /** @test */
    public function testFormIsNotRenderedForRandomUser()
    {
        $this->signIn();

        $this->get($this->thread->path())
            ->assertDontSeeLivewire('thread-poll-form');
    }

    /** @test */
    public function testFormIsRenderedForUserWithPermission()
    {
        $this->signInWithPermission('threads.edit');

        $this->get($this->thread->path())
            ->assertSeeLivewire('thread-poll-form');
    }

    /** @test */
    public function testFormIsRenderedForThreadCreator()
    {
        $this->get($this->thread->path())
            ->assertSeeLivewire('thread-poll-form');
    }

    /** @test */
    public function testCanAttachPollToThread()
    {
        $this->attachPollToThread()
            ->assertEmitted('pollUpdated');

        $this->assertDatabaseHas('polls', ['thread_id' => $this->thread->id]);
    }

    /** @test */
    public function testPollMustHaveAtLeastTwoOptions()
    {
        $this->attachPollToThread(['options' => []])
            ->assertHasErrors(['state.options' => 'required']);

        $this->attachPollToThread(['options' => [
            ['label' => 'Option 1', 'color' => '#FFFFFF'],
        ]])
            ->assertHasErrors(['state.options' => 'min']);

        $this->attachPollToThread(['options' => [
            ['label' => 'Option 1', 'color' => '#FFFFFF'],
            ['label' => 'Option 2', 'color' => '#FFFFFF'],
        ]])
            ->assertHasNoErrors(['state.options']);
    }

    /** @test */
    public function testPollOptionLabelIsRequired()
    {
        $this->attachPollToThread(['options' => [
            ['label' => null, 'color' => '#FFFFFF'],
        ]])
            ->assertHasErrors(['state.options.0.label' => 'required']);
    }

    /** @test */
    public function testPollOptionLabelCannotBeLongerThan255Characters()
    {
        $this->attachPollToThread(['options' => [
            ['label' => str_repeat('*', 256), 'color' => '#FFFFFF'],
        ]])
            ->assertHasErrors(['state.options.0.label' => 'max']);
    }

    /** @test */
    public function testPollOptionColorIsOptional()
    {
        $this->attachPollToThread(['options' => [
            ['label' => 'Cool label', 'color' => null],
        ]])
            ->assertHasNoErrors(['state.options.0.color']);
    }

    /** @test */
    public function testPollOptionColorMustBeHexColor()
    {
        $this->attachPollToThread(['options' => [
            ['label' => 'Cool label', 'color' => 'foobar'],
        ]])
            ->assertHasErrors(['state.options.0.color' => 'regex']);

        $this->attachPollToThread(['options' => [
            ['label' => 'Cool label', 'color' => '#ffffff'],
        ]])
            ->assertHasNoErrors(['state.options.0.color']);
    }

    /** @test */
    public function testPollOptionColorCanBe3CharHexColor()
    {
        $this->attachPollToThread(['options' => [
            ['label' => 'Cool label', 'color' => '#fff'],
        ]])
            ->assertHasNoErrors(['state.options.0.color']);
    }

    /** @test */
    public function testVotesEditableIsRequired()
    {
        $this->attachPollToThread(['votes_editable' => null])
            ->assertHasErrors(['state.votes_editable' => 'required']);
    }

    /** @test */
    public function testVotesEditableMustBeBoolean()
    {
        $this->attachPollToThread(['votes_editable' => 'foobar'])
            ->assertHasErrors(['state.votes_editable' => 'boolean']);
    }

    /** @test */
    public function testMaxVotesIsOptional()
    {
        $this->attachPollToThread(['max_votes' => null])
            ->assertHasNoErrors(['state.max_votes']);
    }

    /** @test */
    public function testMaxVotesMustBeInteger()
    {
        $this->attachPollToThread(['max_votes' => 'foobar'])
            ->assertHasErrors(['state.max_votes' => 'integer']);
    }

    /** @test */
    public function testMaxVotesMustBeGreaterOrEqualTo1()
    {
        $this->attachPollToThread(['max_votes' => 0])
            ->assertHasErrors(['state.max_votes' => 'min']);
    }

    /** @test */
    public function testMaxVotesMustBeLessOrEqualToOptionsCount()
    {
        $this->attachPollToThread([
            'options' => [
                ['label' => 'Option 1', 'color' => '#FFFFFF'],
                ['label' => 'Option 2', 'color' => '#FFFFFF'],
            ],
            'max_votes' => 3,
        ])
            ->assertHasErrors(['state.max_votes' => 'max']);
    }

    /** @test */
    public function testVotesPrivacyIsRequired()
    {
        $this->attachPollToThread(['votes_privacy' => null])
            ->assertHasErrors(['state.votes_privacy' => 'required']);
    }

    /** @test */
    public function testVotesPrivacyMustBeValidValue()
    {
        $this->attachPollToThread(['votes_privacy' => 'foobar'])
            ->assertHasErrors(['state.votes_privacy' => 'in']);

        $this->attachPollToThread(['votes_privacy' => 1])
            ->assertHasErrors(['state.votes_privacy' => 'in']);

        $this->attachPollToThread(['votes_privacy' => 'anonymous'])
            ->assertHasNoErrors(['state.votes_privacy']);

        $this->attachPollToThread(['votes_privacy' => 'private'])
            ->assertHasNoErrors(['state.votes_privacy']);

        $this->attachPollToThread(['votes_privacy' => 'public'])
            ->assertHasNoErrors(['state.votes_privacy']);
    }

    /** @test */
    public function testResultsBeforeVotingIsRequired()
    {
        $this->attachPollToThread(['results_before_voting' => null])
            ->assertHasErrors(['state.results_before_voting' => 'required']);
    }

    /** @test */
    public function testResultsBeforeVotingMustBeBoolean()
    {
        $this->attachPollToThread(['results_before_voting' => 'foo'])
            ->assertHasErrors(['state.results_before_voting' => 'boolean']);
    }

    /** @test */
    public function testLockedAtIsOptional()
    {
        $this->attachPollToThread(['locked_at' => null])
            ->assertHasNoErrors(['state.locked_at']);
    }

    /** @test */
    public function testLockedAtMustBeDateOfProperFormat()
    {
        $this->attachPollToThread(['locked_at' => 'foobar'])
            ->assertHasErrors(['state.locked_at' => 'date_format']);

        $this->attachPollToThread(['locked_at' => 1603356876])
            ->assertHasErrors(['state.locked_at' => 'date_format']);

        $this->attachPollToThread(['locked_at' => '2020-09-22 05:00:00'])
            ->assertHasNoErrors(['state.locked_at']);
    }

    protected function attachPollToThread(array $data = []): TestableLivewire
    {
        return Livewire::test(ThreadPollForm::class, ['thread' => $this->thread])
            ->set('state', array_merge(
                Poll::factory()->raw(),
                ['options' => PollOption::factory()->count(rand(2, 10))->raw(['poll_id' => null])],
                $data,
            ))
            ->call('save');
    }
}
