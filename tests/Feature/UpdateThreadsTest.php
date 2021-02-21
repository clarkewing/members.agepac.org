<?php

namespace Tests\Feature;

use App\Http\Livewire\Thread;
use App\Models\Thread as ThreadModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = ThreadModel::factory()->create(['user_id' => Auth::id()]);
    }

    /** @test */
    public function testThreadComponentIsRendered()
    {
        $this->get($this->thread->path)
            ->assertSeeLivewire('thread');
    }

    /** @test */
    public function testGuestsCannotUpdateThreads()
    {
        Auth::logout();

        $this->updateThread()
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotUpdateThreads()
    {
        $this->signIn();

        $this->updateThread()
            ->assertForbidden();
    }

    /** @test */
    public function testAThreadCanBeUpdatedByItsCreator()
    {
        $this->updateThread(['title' => 'Changed'])
            ->assertOk();

        $this->assertEquals('Changed', $this->thread->fresh()->title);
    }

    /** @test */
    public function testAThreadCanBeUpdatedByAnAuthorizedUser()
    {
        $this->signInWithPermission('threads.edit');

        $this->updateThread(['title' => 'Changed'])
            ->assertOk();

        $this->assertEquals('Changed', $this->thread->fresh()->title);
    }

    /** @test */
    public function testAThreadRequiresATitleToBeUpdated()
    {
        $this->updateThread(['title' => null])
            ->assertHasErrors(['state.title' => 'required']);
    }

    /**
     * @param  array  $data
     * @return \Livewire\Testing\TestableLivewire
     */
    protected function updateThread(array $data = []): \Livewire\Testing\TestableLivewire
    {
        return Livewire::test(Thread::class, [$this->thread])
            ->set('state', $data)
            ->call('update');
    }
}
