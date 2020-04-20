<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testAUserCanFetchTheirUnreadNotifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(
            1,
            $this->getJson('/profiles/' . Auth::user()->name . '/notifications')->json()
        );
    }

    /**
     * @test
     */
    public function testAUserCanMarkANotificationAsRead()
    {
        create(DatabaseNotification::class);

        $this->assertCount(1, Auth::user()->unreadNotifications);

        $this->delete(
            '/profiles/' . Auth::user()->name . '/notifications/' . Auth::user()->unreadNotifications()->first()->id
        );

        $this->assertCount(0, Auth::user()->fresh()->unreadNotifications);
    }
}
