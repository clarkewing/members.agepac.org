<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testUnauthorizedUsersMayNotUpdateThreads()
    {
        $thread = Thread::factory()->create();

        $this->patch($thread->path(), [])
            ->assertStatus(403);
    }

    /** @test */
    public function testAThreadRequiresATitleToBeUpdated()
    {
        $thread = Thread::factory()->create(['user_id' => Auth::id()]);

        $this->patch($thread->path(), [
            'title' => null,
        ])->assertSessionHasErrors('title');
    }

    /** @test */
    public function testAThreadCanBeUpdatedByItsCreator()
    {
        $thread = Thread::factory()->create(['user_id' => Auth::id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed',
        ])->assertOk();

        $this->assertEquals('Changed', $thread->fresh()->title);
    }

    /** @test */
    public function testAThreadCanBeUpdatedByAnAuthorizedUser()
    {
        $thread = Thread::factory()->create();

        $this->signInWithPermission('threads.edit');

        $this->patch($thread->path(), [
            'title' => 'Changed',
        ])->assertOk();

        $this->assertEquals('Changed', $thread->fresh()->title);
    }
}
