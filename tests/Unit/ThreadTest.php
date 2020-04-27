<?php

namespace Tests\Unit;

use App\Channel;
use App\Notifications\ThreadWasUpdated;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create(Thread::class);
    }

    /**
     * @test
     */
    public function testHasAPath()
    {
        $this->assertEquals(
            route('threads.show', [$this->thread->channel, $this->thread]),
            $this->thread->path()
        );
    }

    /**
     * @test
     */
    public function testHasACreator()
    {
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    /**
     * @test
     */
    public function testHasReplies()
    {
        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    /**
     * @test
     */
    public function testCanAddAReply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1,
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /**
     * @test
     */
    public function testNotifiesAllSubscribersWhenAReplyIsAdded()
    {
        Notification::fake();

        $this->signIn()
            ->thread
            ->subscribe()
            ->addReply([
                'body' => 'Foobar',
                'user_id' => create(User::class)->id,
            ]);

        Notification::assertSentTo(Auth::user(), ThreadWasUpdated::class);
    }

    /**
     * @test
     */
    public function testBelongsToAChannel()
    {
        $this->assertInstanceOf(Channel::class, $this->thread->channel);
    }

    /**
     * @test
     */
    public function testCanBeSubscribedTo()
    {
        $this->thread->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $this->thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /**
     * @test
     */
    public function testCanBeUnsubscribedFrom()
    {
        $this->thread->subscribe($userId = 1);
        $this->thread->unsubscribe($userId = 1);

        $this->assertEquals(
            0,
            $this->thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /**
     * @test
     */
    public function testKnowsIfAuthenticatedUserIsSubscribedToIt()
    {
        $this->signIn();

        $this->assertFalse($this->thread->isSubscribedTo);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);
    }

    /**
     * @test
     */
    public function testCanCheckIfTheAuthenticatedUserHasReadAllReplies()
    {
        $this->signIn($user = create(User::class));

        $this->assertTrue($this->thread->hasUpdatesFor($user));

        $user->read($this->thread);

        $this->assertFalse($this->thread->hasUpdatesFor($user));
    }

    /**
     * @test
     */
    public function testRecordsEachVisit()
    {
        $this->thread->visits()->reset();

        $this->assertSame(0, $this->thread->visits()->count());

        $this->thread->visits()->record();

        $this->assertEquals(1, $this->thread->visits()->count());

        $this->thread->visits()->record();

        $this->assertEquals(2, $this->thread->visits()->count());
    }

    /**
     * @test
     */
    public function testBodyIsSanitizedAutomatically()
    {
        $thread = make(Thread::class, ['body' => '<script>alert("bad");</script><p>This is okay.</p>']);

        $this->assertEquals($thread->body, '<p>This is okay.</p>');
    }

    /**
     * @test
     */
    public function testCanHaveABestReply()
    {
        $reply = create(Reply::class, ['thread_id' => $this->thread->id]);

        $this->thread->markBestReply($reply);

        $this->assertEquals($reply->id, $this->thread->bestReply->id);
    }
}
