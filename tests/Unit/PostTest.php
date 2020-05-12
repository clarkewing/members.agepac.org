<?php

namespace Tests\Unit;

use App\Post;
use App\User;
use Tests\TestCase;

class PostTest extends TestCase
{
    /** @test */
    public function testHasAnOwner()
    {
        $post = create(Post::class);

        $this->assertInstanceOf(User::class, $post->owner);
    }

    /** @test */
    public function testKnowsIfItWasJustPublished()
    {
        $post = create(Post::class);

        $this->assertTrue($post->wasJustPublished());

        $post->created_at = now()->subMonth();

        $this->assertFalse($post->wasJustPublished());
    }

    /** @test */
    public function testDetectsAllMentionedUsersInTheBody()
    {
        $jane = create(User::class, ['username' => 'jane.doe']);
        $john = create(User::class, ['username' => 'john.doe']);

        $post = new Post([
            'body' => '@jane.doe wants to talk to @john.doe but not @fake.user',
        ]);

        $this->assertCount(2, $post->mentionedUsers());
        $this->assertTrue($post->mentionedUsers()->contains('id', $jane->id));
        $this->assertTrue($post->mentionedUsers()->contains('id', $john->id));
    }

    /** @test */
    public function testWrapsMentionedUsernamesInTheBodyWithinAnchorTags()
    {
        $post = new Post([
            'body' => 'Hello @jane.doe.',
        ]);

        $this->assertEquals(
            'Hello <a href="' . route('profiles.show', 'jane.doe', false) . '">@jane.doe</a>.',
            $post->body
        );
    }

    /** @test */
    public function testKnowsIfItIsTheBestPost()
    {
        $post = create(Post::class);

        $this->assertFalse($post->isBest());

        $post->thread->update(['best_post_id' => $post->id]);

        $this->assertTrue($post->fresh()->isBest());
    }

    /** @test */
    public function testBodyIsSanitizedAutomatically()
    {
        $post = make(Post::class, ['body' => '<script>alert("bad");</script><p>This is okay.</p>']);

        $this->assertEquals($post->body, '<p>This is okay.</p>');
    }
}
