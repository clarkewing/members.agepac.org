<?php

namespace Tests\Feature\Nova;

use App\Models\Thread;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageThreadsTest extends TestCase
{
    use NovaTestRequests;

    /** @test */
    public function testUnauthorizedUsersCannotIndexThreads()
    {
        $this->signIn();

        $this->indexResource('threads')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewAThread()
    {
        $thread = Thread::factory()->create();

        $this->signIn();

        $this->showResource('threads', $thread->id)
            ->assertForbidden();
    }

    /** @test */
    public function testCreatingAThreadInNovaIsForbidden()
    {
        $this->signIn();

        $this->storeResource('threads', Thread::factory()->make(['title' => 'Fake'])->toArray())
            ->assertForbidden();

        $this->signInWithPermission('threads.edit');

        $this->storeResource('threads', Thread::factory()->make(['title' => 'Fake'])->toArray())
            ->assertForbidden();

        $this->assertDatabaseMissing('threads', ['title' => 'Fake']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditAThread()
    {
        $thread = Thread::factory()->create(['title' => 'Foobar']);

        $this->signIn();

        $this->updateThread(['title' => 'Fake title'], $thread)
            ->assertForbidden();

        $this->assertEquals('Foobar', $thread->fresh()->title);
    }

    /** @test */
    public function testDeletingAThreadInNovaIsForbidden()
    {
        $thread = Thread::factory()->create();

        $this->signIn();

        $this->deleteResource('threads', $thread->id)
            ->assertForbidden();

        $this->signInWithPermission('threads.delete');

        $this->deleteResource('threads', $thread->id)
            ->assertForbidden();

        $this->assertDatabaseHas('threads', ['id' => $thread->id]);
    }

    /** @test */
    public function testAuthorizedUsersCanIndexThreads()
    {
        $this->signInWithPermission('threads.edit');

        $this->indexResource('threads')
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanViewAThread()
    {
        $thread = Thread::factory()->create();

        $this->signInWithPermission('threads.edit');

        $this->showResource('threads', $thread->id)
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanEditAThread()
    {
        $thread = Thread::factory()->create();

        $this->signInWithPermission('threads.edit');

        $this->updateThread(['title' => 'Updated Thread'], $thread)
            ->assertOk();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'title' => 'Updated Thread']);
    }

    /** @test */
    public function testTitleIsRequired()
    {
        $this->signInWithPermission('threads.edit');

        $this->updateThread(['title' => null])
            ->assertJsonValidationErrors('title');
    }

    /**
     * Submits a request to update an existing thread.
     *
     * @param  array  $data
     * @param  \App\Models\Thread|null  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    public function updateThread(array $data = [], Thread $thread = null)
    {
        $thread = $thread ?? Thread::factory()->create();

        return $this->updateResource(
            'threads',
            $thread->id,
            array_merge($thread->toArray(), $data)
        );
    }
}
