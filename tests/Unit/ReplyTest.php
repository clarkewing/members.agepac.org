<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    /** @test */
    public function testHasAnOwner()
    {
        $reply = create(Reply::class);

        $this->assertInstanceOf(User::class, $reply->owner);
    }

    /** @test */
    public function testKnowsIfItWasJustPublished()
    {
        $reply = create(Reply::class);

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function testDetectsAllMentionedUsersInTheBody()
    {
        $jane = create(User::class, ['name' => 'JaneDoe']);
        $john = create(User::class, ['name' => 'JohnDoe']);

        $reply = new Reply([
            'body' => '@JaneDoe wants to talk to @JohnDoe but not @FakeUser',
        ]);

        $this->assertCount(2, $reply->mentionedUsers());
        $this->assertTrue($reply->mentionedUsers()->contains('id', $jane->id));
        $this->assertTrue($reply->mentionedUsers()->contains('id', $john->id));
    }

    /** @test */
    public function testWrapsMentionedUsernamesInTheBodyWithinAnchorTags()
    {
        $reply = new Reply([
            'body' => 'Hello @JaneDoe.',
        ]);

        $this->assertEquals(
            'Hello <a href="' . route('profiles.show', 'JaneDoe', false) . '">@JaneDoe</a>.',
            $reply->body
        );
    }

    /** @test */
    public function testKnowsIfItIsTheBestReply()
    {
        $reply = create(Reply::class);

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());
    }

    /** @test */
    public function testBodyIsSanitizedAutomatically()
    {
        $reply = make(Reply::class, ['body' => '<script>alert("bad");</script><p>This is okay.</p>']);

        $this->assertEquals($reply->body, '<p>This is okay.</p>');
    }
}
