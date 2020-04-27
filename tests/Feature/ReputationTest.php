<?php

namespace Tests\Feature;

use App\Reply;
use App\Reputation;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ReputationTest extends TestCase
{
    /**
     * @test
     */
    public function testUserEarnsPointsWhenCreatingAThread()
    {
        $thread = create(Thread::class);

        $this->assertEquals(Reputation::THREAD_PUBLISHED, $thread->creator->reputation);
    }

    /**
     * @test
     */
    public function testUserLosesPointsWhenTheirThreadIsDeleted()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);

        $this->assertEquals(Reputation::THREAD_PUBLISHED, $thread->creator->reputation);

        $this->delete($thread->path());

        $this->assertEquals(0, $thread->creator->fresh()->reputation);
    }

    /**
     * @test
     */
    public function testUserEarnsPointsWhenReplyingToAThread()
    {
        $thread = create(Thread::class);

        $reply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Here is a reply.',
        ]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);
    }

    /**
     * @test
     */
    public function testUserLosesPointsWhenTheirReplyToAThreadIsDeleted()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => Auth::id()]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);

        $this->delete(route('replies.destroy', $reply));

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }

    /**
     * @test
     */
    public function testUserEarnsPointsWhenTheirReplyIsMarkedAsBest()
    {
        $thread = create(Thread::class);

        $thread->markBestReply($reply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Here is a reply.',
        ]));

        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $reply->owner->reputation);
    }

    public function testOnBestReplyChangeThePointsShouldBeTransferred()
    {
        $thread = create(Thread::class);
        [$firstReply, $secondReply] = create(Reply::class, [], 2);

        $thread->markBestReply($firstReply);

        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $firstReply->owner->reputation);

        // If the owner of the thread decides to choose a different best reply...
        $thread->markBestReply($secondReply);

        // Then the original recipient of the best reply reputation should be stripped of those points.
        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED - Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $firstReply->owner->fresh()->reputation);

        // And those points should now be reflected on the account of the new best reply owner.
        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $secondReply->owner->reputation);
    }

    /**
     * @test
     */
    public function testUserEarnsPointsWhenTheirReplyIsFavorited()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $reply->favorite();

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);
    }

    /**
     * @test
     */
    public function testUserLosesPointsWhenTheirReplyIsUnfavorited()
    {
        $reply = create(Reply::class);

        $this->signIn();

        $reply->favorite();

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);

        $reply->unfavorite();

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED - Reputation::REPLY_FAVORITED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);

        $this->assertEquals(0, Auth::user()->reputation);
    }
}
