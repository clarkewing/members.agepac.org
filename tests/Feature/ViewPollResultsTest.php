<?php

namespace Tests\Feature;

use App\Models\Poll;
use Livewire\Livewire;
use Tests\TestCase;

class ViewPollResultsTest extends TestCase
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
    public function testIfAllowedWithoutVotingCanSeeButtonToViewResults()
    {
        $this->poll->update(['results_before_voting' => true]);

        $this->viewBallot()
            ->assertSee('Voir les résultats');
    }

    /** @test */
    public function testIfForbiddenWithoutVotingCannotSeeButtonToViewResults()
    {
        $this->poll->update(['results_before_voting' => false]);

        $this->viewBallot()
            ->assertDontSee('Voir les résultats');

        // User must vote to see results.
        $this->castVote();

        $this->viewBallot()
            ->assertSee('Voir les résultats');
    }

    /** @test */
    public function testPollCreatorCanAlwaysSeeButtonToViewResults()
    {
        $this->poll->update(['results_before_voting' => false]);

        $this->signIn($this->poll->thread->creator);

        $this->viewBallot()
            ->assertSee('Voir les résultats');
    }

    /** @test */
    public function testUsersWithPermissionsCanAlwaysSeeButtonToViewResults()
    {
        $this->poll->update(['results_before_voting' => false]);

        $this->signInWithPermission('threads.edit');

        $this->viewBallot()
            ->assertSee('Voir les résultats');
    }

    /** @test */
    public function testIfAllowedWithoutVotingCanViewResults()
    {
        $this->poll->update(['results_before_voting' => true]);

        $this->viewResults()
            ->assertOk();
    }

    /** @test */
    public function testIfForbiddenWithoutVotingCannotViewResults()
    {
        $this->poll->update(['results_before_voting' => false]);

        $this->viewResults()
            ->assertForbidden();

        // User must vote to see results.
        $this->castVote();

        $this->viewResults()
            ->assertOk();
    }

    /** @test */
    public function testPollCreatorCanAlwaysViewResults()
    {
        $this->poll->update(['results_before_voting' => false]);

        $this->signIn($this->poll->thread->creator);

        $this->viewResults()
            ->assertOk();
    }

    /** @test */
    public function testUsersWithPermissionsCanAlwaysViewResults()
    {
        $this->poll->update(['results_before_voting' => false]);

        $this->signInWithPermission('threads.edit');

        $this->viewResults()
            ->assertOk();
    }

    /** @test */
    public function testShowingVotersOpensModal()
    {
        $this->poll->update(['votes_privacy' => 'public', 'results_before_voting' => true]);

        $option = $this->poll->options->first();

        $component = $this->viewResults()
            ->call('showVoters', $option->id);

        $component
            ->assertSeeHtml('id="votersModal"')
            ->assertDispatchedBrowserEvent('showVoters');

        $this->assertTrue($component->get('modalOption')->is($option));
    }

    /** @test */
    public function testHidingVotersRemovesModal()
    {
        $this->poll->update(['votes_privacy' => 'public', 'results_before_voting' => true]);

        $component = $this->viewResults()
            ->call('showVoters', $this->poll->options->first()->id);

        $component->call('hideVoters')
            ->assertDontSeeHtml('id="votersModal"')
            ->assertSet('modalOption', null);
    }

    /** @test */
    public function testUsersCanViewPublicVotes()
    {
        $this->poll->update(['votes_privacy' => 'public', 'results_before_voting' => true]);

        $this->assertCanViewVotes();
    }

    /** @test */
    public function testUsersCannotViewPrivateVotes()
    {
        $this->poll->update(['votes_privacy' => 'private', 'results_before_voting' => true]);

        $this->assertCannotViewVotes();
    }

    /** @test */
    public function testThreadCreatorCanViewPrivateVotes()
    {
        $this->poll->update(['votes_privacy' => 'private', 'results_before_voting' => true]);

        $this->signIn($this->poll->thread->creator);

        $this->assertCanViewVotes();
    }

    /** @test */
    public function testUsersWithPermissionCanViewPrivateVotes()
    {
        $this->poll->update(['votes_privacy' => 'private', 'results_before_voting' => true]);

        $this->signInWithPermission('threads.edit');

        $this->assertCanViewVotes();
    }

    /** @test */
    public function testNoOneCanViewAnonymousVotes()
    {
        $this->poll->update(['votes_privacy' => 'anonymous', 'results_before_voting' => true]);

        $this->signIn();
        $this->assertCannotViewVotes();

        $this->signIn($this->poll->thread->creator);
        $this->assertCannotViewVotes();

        $this->signInWithPermission('threads.edit');
        $this->assertCannotViewVotes();
    }

    protected function viewBallot(): \Livewire\Testing\TestableLivewire
    {
        return Livewire::test('thread-poll', ['thread' => $this->poll->thread])
            ->call('showBallot');
    }

    protected function viewResults(): \Livewire\Testing\TestableLivewire
    {
        return Livewire::test('thread-poll', ['thread' => $this->poll->thread])
            ->call('showResults');
    }

    protected function castVote(): void
    {
        $this->poll->castVote([$this->poll->options->first()->id]);
    }

    protected function assertCanViewVotes(): void
    {
        $option = $this->poll->options->first();

        $this->viewResults()
            ->assertSee("showVoters($option->id)")
            ->call('showVoters', $option->id)
            ->assertOk();
    }

    protected function assertCannotViewVotes(): void
    {
        $option = $this->poll->options->first();

        $this->viewResults()
            ->assertDontSee("showVoters($option->id)")
            ->call('showVoters', $option->id)
            ->assertForbidden();
    }
}
