<?php

namespace Tests\Feature;

use App\Http\Livewire\ThreadPollForm;
use App\Models\Poll;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;
use Tests\TestCase;

class EditPollTest extends TestCase
{
    /** @var \App\Models\Poll */
    protected $poll;

    protected function setUp(): void
    {
        parent::setUp();

        $this->poll = Poll::factory()->create();

        $this->withExceptionHandling()->signIn($this->poll->thread->creator);
    }

    /** @test */
    public function testRandomUserCannotSeeButtonToEditPoll()
    {
        $this->signIn();

        $this->get($this->poll->thread->path())
            ->assertDontSee('Modifier le Sondage');
    }

    /** @test */
    public function testUserWithPermissionCanSeeButtonToEditPoll()
    {
        $this->signInWithPermission('threads.edit');

        $this->get($this->poll->thread->path())
            ->assertSee('Modifier le Sondage');
    }

    /** @test */
    public function testThreadCreatorCanSeeButtonToEditPoll()
    {
        $this->get($this->poll->thread->path())
            ->assertSee('Modifier le Sondage');
    }

    /** @test */
    public function testButtonToEditPollIsHiddenIfPollIsLocked()
    {
        $this->poll->update(['locked_at' => now()]);

        $this->get($this->poll->thread->path())
            ->assertDontSee('Modifier le Sondage');
    }

    /** @test */
    public function testButtonToEditPollIsHiddenIfThreadIsLocked()
    {
        $this->poll->thread->update(['locked' => true]);

        $this->get($this->poll->thread->path())
            ->assertDontSee('Modifier le Sondage');
    }

    /** @test */
    public function testButtonToEditPollIsHiddenIfItHasReceivedVotes()
    {
        $this->poll->castVote([$this->poll->options[0]->id]);

        $this->get($this->poll->thread->path())
            ->assertDontSee('Modifier le Sondage');
    }

    /** @test */
    public function testFormIsNotRenderedForRandomUser()
    {
        $this->signIn();

        $this->get($this->poll->thread->path())
            ->assertDontSeeLivewire('thread-poll-form');
    }

    /** @test */
    public function testFormIsRenderedForUserWithPermission()
    {
        $this->signInWithPermission('threads.edit');

        $this->get($this->poll->thread->path())
            ->assertSeeLivewire('thread-poll-form');
    }

    /** @test */
    public function testFormIsRenderedForThreadCreator()
    {
        $this->get($this->poll->thread->path())
            ->assertSeeLivewire('thread-poll-form');
    }

    /** @test */
    public function testCannotUpdatePollIfItIsLocked()
    {
        $this->poll->update(['locked_at' => now()]);

        $this->updatePoll()
            ->assertForbidden();
    }

    /** @test */
    public function testCannotUpdatePollIfThreadIsLocked()
    {
        $this->poll->thread->update(['locked' => true]);

        $this->updatePoll()
            ->assertForbidden();
    }

    /** @test */
    public function testCannotUpdatePollIfItHasReceivedVotes()
    {
        $this->poll->castVote([$this->poll->options[0]->id]);

        $this->updatePoll()
            ->assertForbidden();
    }

    /** @test */
    public function testPollCanBeUpdated()
    {
        $this->updatePoll()
            ->assertEmitted('pollUpdated');
    }

    /** @test */
    public function testPollMustHaveAtLeastTwoOptions()
    {
        $this->updatePoll(['options' => []])
            ->assertHasErrors(['state.options' => 'required']);

        $this->updatePoll(['options' => [
            ['label' => 'Option 1', 'color' => '#FFFFFF'],
        ]])
            ->assertHasErrors(['state.options' => 'min']);

        $this->updatePoll(['options' => [
            ['label' => 'Option 1', 'color' => '#FFFFFF'],
            ['label' => 'Option 2', 'color' => '#FFFFFF'],
        ]])
            ->assertHasNoErrors(['state.options']);
    }

    /** @test */
    public function testPollOptionLabelIsRequired()
    {
        $this->updatePoll(['options' => [
            ['label' => null, 'color' => '#FFFFFF'],
        ]])
            ->assertHasErrors(['state.options.0.label' => 'required']);
    }

    /** @test */
    public function testPollOptionLabelCannotBeLongerThan255Characters()
    {
        $this->updatePoll(['options' => [
            ['label' => str_repeat('*', 256), 'color' => '#FFFFFF'],
        ]])
            ->assertHasErrors(['state.options.0.label' => 'max']);
    }

    /** @test */
    public function testPollOptionColorIsOptional()
    {
        $this->updatePoll(['options' => [
            ['label' => 'Cool label', 'color' => null],
        ]])
            ->assertHasNoErrors(['state.options.0.color']);
    }

    /** @test */
    public function testPollOptionColorMustBeHexColor()
    {
        $this->updatePoll(['options' => [
            ['label' => 'Cool label', 'color' => 'foobar'],
        ]])
            ->assertHasErrors(['state.options.0.color' => 'regex']);

        $this->updatePoll(['options' => [
            ['label' => 'Cool label', 'color' => '#ffffff'],
        ]])
            ->assertHasNoErrors(['state.options.0.color']);
    }

    /** @test */
    public function testPollOptionColorCanBe3CharHexColor()
    {
        $this->updatePoll(['options' => [
            ['label' => 'Cool label', 'color' => '#fff'],
        ]])
            ->assertHasNoErrors(['state.options.0.color']);
    }

    /** @test */
    public function testVotesEditableIsRequired()
    {
        $this->updatePoll(['votes_editable' => null])
            ->assertHasErrors(['state.votes_editable' => 'required']);
    }

    /** @test */
    public function testVotesEditableMustBeBoolean()
    {
        $this->updatePoll(['votes_editable' => 'foobar'])
            ->assertHasErrors(['state.votes_editable' => 'boolean']);
    }

    /** @test */
    public function testMaxVotesIsOptional()
    {
        $this->updatePoll(['max_votes' => null])
            ->assertHasNoErrors(['state.max_votes']);
    }

    /** @test */
    public function testMaxVotesMustBeInteger()
    {
        $this->updatePoll(['max_votes' => 'foobar'])
            ->assertHasErrors(['state.max_votes' => 'integer']);
    }

    /** @test */
    public function testMaxVotesMustBeGreaterOrEqualTo1()
    {
        $this->updatePoll(['max_votes' => 0])
            ->assertHasErrors(['state.max_votes' => 'min']);
    }

    /** @test */
    public function testMaxVotesMustBeLessOrEqualToOptionsCount()
    {
        $this->updatePoll([
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
        $this->updatePoll(['votes_privacy' => null])
            ->assertHasErrors(['state.votes_privacy' => 'required']);
    }

    /** @test */
    public function testVotesPrivacyMustBeValidValue()
    {
        $this->updatePoll(['votes_privacy' => 'foobar'])
            ->assertHasErrors(['state.votes_privacy' => 'in']);

        $this->updatePoll(['votes_privacy' => 1])
            ->assertHasErrors(['state.votes_privacy' => 'in']);

        $this->updatePoll(['votes_privacy' => 'anonymous'])
            ->assertHasNoErrors(['state.votes_privacy']);

        $this->updatePoll(['votes_privacy' => 'private'])
            ->assertHasNoErrors(['state.votes_privacy']);

        $this->updatePoll(['votes_privacy' => 'public'])
            ->assertHasNoErrors(['state.votes_privacy']);
    }

    /** @test */
    public function testResultsBeforeVotingIsRequired()
    {
        $this->updatePoll(['results_before_voting' => null])
            ->assertHasErrors(['state.results_before_voting' => 'required']);
    }

    /** @test */
    public function testResultsBeforeVotingMustBeBoolean()
    {
        $this->updatePoll(['results_before_voting' => 'foo'])
            ->assertHasErrors(['state.results_before_voting' => 'boolean']);
    }

    /** @test */
    public function testLockedAtIsOptional()
    {
        $this->updatePoll(['locked_at' => null])
            ->assertHasNoErrors(['state.locked_at']);
    }

    /** @test */
    public function testLockedAtMustBeDateOfProperFormat()
    {
        $this->updatePoll(['locked_at' => 'foobar'])
            ->assertHasErrors(['state.locked_at' => 'date_format']);

        $this->updatePoll(['locked_at' => 1603356876])
            ->assertHasErrors(['state.locked_at' => 'date_format']);

        $this->updatePoll(['locked_at' => '2020-09-22 05:00:00'])
            ->assertHasNoErrors(['state.locked_at']);
    }

    protected function updatePoll(array $data = []): TestableLivewire
    {
       return Livewire::test(ThreadPollForm::class, ['thread' => $this->poll->thread])
            ->set('state', array_merge(
                $this->poll->toArray(),
                ['options' => $this->poll->options->toArray()],
                $data,
            ))
            ->call('save');
    }
}
