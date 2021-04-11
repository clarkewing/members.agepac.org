<?php

namespace Tests\Feature;

use App\Http\Livewire\ThreadPoll;
use App\Models\Poll;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;
use Tests\TestCase;

class DeletePollTest extends TestCase
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
    public function testRandomUserCannotSeeButtonToDeletePoll()
    {
        $this->signIn();

        $this->get($this->poll->thread->path())
            ->assertDontSee('Supprimer le Sondage');
    }

    /** @test */
    public function testUserWithPermissionCanSeeButtonToDeletePoll()
    {
        $this->signInWithPermission('threads.edit');

        $this->get($this->poll->thread->path())
            ->assertSee('Supprimer le Sondage');
    }

    /** @test */
    public function testThreadCreatorCanSeeButtonToDeletePoll()
    {
        $this->get($this->poll->thread->path())
            ->assertSee('Supprimer le Sondage');
    }

    /** @test */
    public function testButtonToDeletePollIsHiddenIfThreadIsLocked()
    {
        $this->poll->thread->update(['locked' => true]);

        $this->get($this->poll->thread->path())
            ->assertDontSee('Supprimer le Sondage');
    }

    /** @test */
    public function testThreadCreatorCanDeletePoll()
    {
        $this->deletePoll()
            ->assertEmitted('pollDeleted');
    }

    /** @test */
    public function testUserWithPermissionCanDeletePoll()
    {
        $this->signInWithPermission('threads.edit');

        $this->deletePoll()
            ->assertEmitted('pollDeleted');
    }

    /** @test */
    public function testPollCannotBeDeletedIfItsThreadIsLocked()
    {
        $this->poll->thread->update(['locked' => true]);

        $this->deletePoll()
            ->assertNotEmitted('pollDeleted');
    }

    protected function deletePoll(): TestableLivewire
    {
        return Livewire::test(ThreadPoll::class, ['thread' => $this->poll->thread])
            ->call('delete');
    }
}
