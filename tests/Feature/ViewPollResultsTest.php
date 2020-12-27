<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\User;
use Tests\TestCase;

class ViewPollResultsTest extends TestCase
{
    /** @var \App\Models\Poll */
    protected $poll;

    protected function setUp(): void
    {
        parent::setUp();

        $this->poll = Poll::factory()->create();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testGuestsCannotViewPollResults()
    {
        $this->getPollResults()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotViewPollResults()
    {
        $this->signInUnsubscribed();

        $this->getPollResults()
            ->assertPaymentRequired();
    }

    /** @test */
    public function testIfAllowedSubscribedUsersCanViewResultsBeforeVoting()
    {
        $this->signIn();

        $this->poll->update(['results_before_voting' => true]);

        $results = $this->getPollResults()
            ->assertOk()
            ->json();

        $this->assertArrayHasKey('votes_count', $results[0]);
        $this->assertArrayHasKey('votes_percent', $results[0]);
    }

    /** @test */
    public function testIfNotAllowedSubscribedUsersCannotViewResultsBeforeVoting()
    {
        $this->signIn();

        $this->poll->update(['results_before_voting' => false]);

        $this->getPollResults()
            ->assertForbidden();

        // User must vote to see results.
        $this->poll->castVote([$this->poll->options[0]->id]);

        $this->getPollResults()
            ->assertOk();
    }

    /** @test */
    public function testPollCreatorCanViewResultsWithoutVoting()
    {
        $this->signIn($this->poll->thread->creator);

        $this->poll->update(['results_before_voting' => false]);

        $this->getPollResults()
            ->assertOk();
    }

    /** @test */
    public function testUsersWithPermissionsCanViewResultsWithoutVoting()
    {
        $this->signInWithPermission('threads.edit');

        $this->poll->update(['results_before_voting' => false]);

        $this->getPollResults()
            ->assertOk();
    }

    /** @test */
    public function testIfVotesArePublicSubscribedUsersCanViewVotes()
    {
        $this->signIn();

        $this->poll->update(['votes_privacy' => 'public', 'results_before_voting' => true]);

        $this->assertArrayHasKey('voters', $this->getPollResults()->json()[0]);
    }

    /** @test */
    public function testIfVotesArePrivateSubscribedUsersCannotViewVotes()
    {
        $this->signIn();

        $this->poll->update(['votes_privacy' => 'private', 'results_before_voting' => true]);

        $this->assertArrayNotHasKey('voters', $this->getPollResults()->json()[0]);
    }

    /** @test */
    public function testIfVotesArePrivateThreadCreatorCanViewVotes()
    {
        $this->signIn($this->poll->thread->creator);

        $this->poll->update(['votes_privacy' => 'private', 'results_before_voting' => true]);

        $this->assertArrayHasKey('voters', $this->getPollResults()->json()[0]);
    }

    /** @test */
    public function testIfVotesArePrivateUsersWithPermissionCanViewVotes()
    {
        $this->signInWithPermission('threads.edit');

        $this->poll->update(['votes_privacy' => 'private', 'results_before_voting' => true]);

        $this->assertArrayHasKey('voters', $this->getPollResults()->json()[0]);
    }

    /** @test */
    public function testIfVotesAreAnonymousNoOneCanViewVotes()
    {
        $this->poll->update(['votes_privacy' => 'anonymous', 'results_before_voting' => true]);

        $this->actingAs(User::factory()->create())
            ->assertArrayNotHasKey('voters', $this->getPollResults()->json()[0]);

        $this->actingAs($this->poll->thread->creator)
            ->assertArrayNotHasKey('voters', $this->getPollResults()->json()[0]);
    }

    /**
     * Send get request to fetch poll results.
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function getPollResults()
    {
        return $this->getJson(route(
            'poll_results.show',
            [$this->poll->thread->channel, $this->poll->thread],
        ));
    }
}
