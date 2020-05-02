<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function testNotificationIsPreparedWhenASubscribedThreadReceivesANewReplyThatIsNotByTheAuthenticatedUser()
    {
        $thread = create(Thread::class)->subscribe();

        $this->assertCount(0, Auth::user()->notifications);

        $thread->addReply([
            'user_id' => Auth::user()->id,
            'body' => 'This is a reply.',
        ]);

        $this->assertCount(0, Auth::user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'This is a reply.',
        ]);

        $this->assertCount(1, Auth::user()->fresh()->notifications);
    }

    /** @test */
    public function testAUserCanFetchTheirUnreadNotifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(
            1,
            $this->getJson(route('notifications.index'))->json()
        );
    }

    /** @test */
    public function testAUserCanMarkANotificationAsRead()
    {
        create(DatabaseNotification::class);

        $this->assertCount(1, Auth::user()->unreadNotifications);

        $this->delete(route('notifications.destroy', Auth::user()->unreadNotifications()->first()));

        $this->assertCount(0, Auth::user()->fresh()->unreadNotifications);
    }
}
