<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testUnauthorizedUsersMayNotUpdateThreads()
    {
        $thread = create(Thread::class);

        $this->patch($thread->path(), [])
            ->assertStatus(403);
    }

    /** @test */
    public function testAThreadRequiresATitleToBeUpdated()
    {
        $thread = create(Thread::class, ['user_id' => Auth::id()]);

        $this->patch($thread->path(), [
            'title' => null,
        ])->assertSessionHasErrors('title');
    }

    /** @test */
    public function testAThreadCanBeUpdatedByItsCreator()
    {
        $thread = create(Thread::class, ['user_id' => Auth::id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed',
        ]);

        $this->assertEquals('Changed', $thread->fresh()->title);
    }
}
