<?php

namespace Tests\Feature\Notification;

use App\Models\Post;
use App\Notifications\YouWereMentioned;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class YouWereMentionedTest extends TestCase
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

        Notification::assertSentToVia(Auth::user(), 'database', YouWereMentioned::class);
    }

    /** @test */
    public function testNotificationSentToMailChannel()
    {
        $this->notify();

        Notification::assertSentToVia(Auth::user(), 'mail', YouWereMentioned::class);
    }

    protected function notify(): void
    {
        Auth::user()->notify(new YouWereMentioned(Post::factory()->create()));
    }
}
