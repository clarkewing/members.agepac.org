<?php

namespace Tests\Feature\Notification;

use App\Models\Post;
use App\Models\Thread;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadWasUpdatedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        Notification::fake();
    }

    /** @test */
    public function testNotificationSentToDatabaseChannel()
    {
        $this->notify();

        Notification::assertSentToVia(Auth::user(), 'database', ThreadWasUpdated::class);
    }

    /** @test */
    public function testNotificationSentToMailChannel()
    {
        $this->notify();

        Notification::assertSentToVia(Auth::user(), 'mail', ThreadWasUpdated::class);
    }

    protected function notify(): void
    {
        Auth::user()->notify(
            new ThreadWasUpdated(
                $thread = Thread::factory()->create(),
                Post::factory()->create(['thread_id' => $thread->id])
            )
        );
    }
}
