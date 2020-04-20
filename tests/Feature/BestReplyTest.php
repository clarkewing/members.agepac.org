<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class BestReplyTest extends TestCase
{
    /**
     * @test
     */
    public function testThreadCreatorCanMarkAnyReplyAsTheBestReply()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);
        $replies = create(Reply::class, ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', [$replies[1]]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /**
     * @test
     */
    public function testOnlyThreadCreatorMayMarkReplyAsBest()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);
        $replies = create(Reply::class, ['thread_id' => $thread->id], 2);

        $this->signIn();

        $this->postJson(route('best-replies.store', $replies[1]))
            ->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }

    /**
     * @test
     */
    public function testIfABestReplyIsDeletedThenTheThreadIsProperlyUpdated()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => Auth::id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('replies.destroy', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}
