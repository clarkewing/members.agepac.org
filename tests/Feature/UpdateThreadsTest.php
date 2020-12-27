<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = Thread::factory()->create(['user_id' => Auth::id()]);
    }

    /** @test */
    public function testGuestsCannotUpdateThreads()
    {
        Auth::logout();

        $this->updateThread()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotUpdateThreads()
    {
        $this->signInUnsubscribed();

        $this->updateThread()
            ->assertPaymentRequired();
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
            ->assertJsonValidationErrors('title');
    }

    /**
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateThread(array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->patchJson($this->thread->path(), $data);
    }
}
