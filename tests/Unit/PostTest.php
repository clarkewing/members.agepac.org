<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PostTest extends TestCase
{
    /** @test */
    public function testHasAnOwner()
    {
        $post = Post::factory()->create();

        $this->assertInstanceOf(User::class, $post->owner);
    }

    /** @test */
    public function testKnowsIfItWasJustPublished()
    {
        $post = Post::factory()->create();

        $this->assertTrue($post->wasJustPublished());

        $post->created_at = now()->subMonth();

        $this->assertFalse($post->wasJustPublished());
    }

    /** @test */
    public function testDetectsAllMentionedUsersInTheBody()
    {
        $jane = User::factory()->create(['username' => 'jane.doe']);
        $john = User::factory()->create(['username' => 'john.doe']);

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
        User::factory()->create(['username' => 'jane.doe']);

        $post = new Post([
            'body' => 'Hello @jane.doe.',
        ]);

        $this->assertEquals(
            '<p>Hello <a href="' . route('profiles.show', 'jane.doe', false) . '">@jane.doe</a>.</p>',
            $post->body
        );
    }

    /** @test */
    public function testIgnoresMentionedUsernamesForNonExistentUsers()
    {
        $post = new Post([
            'body' => 'Hello @jane.doe.',
        ]);

        $this->assertEquals(
            '<p>Hello @jane.doe.</p>',
            $post->body
        );
    }

    /** @test */
    public function testBodyPreservesAnyAllowedHtmlTags()
    {
        Event::fake();

        $post = Post::factory()->create(['body' => '<h1>Header 1</h1>']);
        $this->assertEquals('<h1>Header 1</h1>', $post->body);

        $post = Post::factory()->create(['body' => '<h2>Header 2</h2>']);
        $this->assertEquals('<h2>Header 2</h2>', $post->body);

        $post = Post::factory()->create(['body' => '<h3>Header 3</h3>']);
        $this->assertEquals('<h3>Header 3</h3>', $post->body);

        $post = Post::factory()->create(['body' => '<h4>Header 4</h4>']);
        $this->assertEquals('<h4>Header 4</h4>', $post->body);

        $post = Post::factory()->create(['body' => '<h5>Header 5</h5>']);
        $this->assertEquals('<h5>Header 5</h5>', $post->body);

        $post = Post::factory()->create(['body' => '<h6>Header 6</h6>']);
        $this->assertEquals('<h6>Header 6</h6>', $post->body);

        $post = Post::factory()->create(['body' => '<b>Bold text</b>']);
        $this->assertEquals('<p><b>Bold text</b></p>', $post->body);

        $post = Post::factory()->create(['body' => '<strong>Strong text</strong>']);
        $this->assertEquals('<p><strong>Strong text</strong></p>', $post->body);

        $post = Post::factory()->create(['body' => '<i>Italic text</i>']);
        $this->assertEquals('<p><i>Italic text</i></p>', $post->body);

        $post = Post::factory()->create(['body' => '<em>Emphasis text</em>']);
        $this->assertEquals('<p><em>Emphasis text</em></p>', $post->body);

        $post = Post::factory()->create(['body' => '<del>Strikethrough text</del>']);
        $this->assertEquals('<p><del>Strikethrough text</del></p>', $post->body);

        $post = Post::factory()->create(['body' => '<a href="/foobar" title="Baz">Link with href and title</a>']);
        $this->assertEquals('<p><a href="/foobar" title="Baz">Link with href and title</a></p>', $post->body);

        $post = Post::factory()->create(['body' => '<ul><li>Unordered list item</li></ul>']);
        $this->assertEquals('<ul><li>Unordered list item</li></ul>', $post->body);

        $post = Post::factory()->create(['body' => '<ol><li>Ordered list item</li></ol>']);
        $this->assertEquals('<ol><li>Ordered list item</li></ol>', $post->body);

        $post = Post::factory()->create(['body' => '<p style="color:#0000FF;">Paragraph with style</p>']);
        $this->assertEquals('<p style="color:#0000FF;">Paragraph with style</p>', $post->body);

        $post = Post::factory()->create(['body' => '<span style="color:#0000FF;">Span with style</span>']);
        $this->assertEquals('<p><span style="color:#0000FF;">Span with style</span></p>', $post->body);

        $post = Post::factory()->create(['body' => 'Line 1<br>Line 2']);
        $this->assertEquals('<p>Line 1<br>Line 2</p>', $post->body);

        $post = Post::factory()->create([
            'body' => '<p><figure class="attachment" data-trix-attachment="foobar" data-trix-attributes="baz" data-trix-content-type="foo/bar">Content</figure></p>',
        ]);
        $this->assertEquals(
            '<p><figure class="attachment" data-trix-attachment="foobar" data-trix-attributes="baz" data-trix-content-type="foo/bar">Content</figure></p>',
            $post->body
        );

        $post = Post::factory()->create([
            'body' => '<p><figcaption class="attachment__caption">This is a caption</figcaption></p>',
        ]);
        $this->assertEquals(
            '<p><figcaption class="attachment__caption">This is a caption</figcaption></p>',
            $post->body
        );

        $post = Post::factory()->create([
            'body' => '<p><img src="/greatimage.jpg" alt="Image with src, alt, width, and height" width="12" height="34"></p>',
        ]);
        $this->assertEquals(
            '<p><img src="/greatimage.jpg" alt="Image with src, alt, width, and height" width="12" height="34"></p>',
            $post->body
        );
    }

    /** @test */
    public function testParagraphsInBodyAreParsed()
    {
        $post = Post::factory()->create(['body' => 'Single line body']);
        $this->assertEquals('<p>Single line body</p>', $post->body);

        $post = Post::factory()->create(['body' => 'A body with a first paragraph<br><br>And another one']);
        $this->assertEquals('<p>A body with a first paragraph</p><p>And another one</p>', $post->body);

        $post = Post::factory()->create(['body' => 'A body with paragraph<br>Spread over a few lines']);
        $this->assertEquals('<p>A body with paragraph<br>Spread over a few lines</p>', $post->body);
    }

    /** @test */
    public function testBodyIsPurifiedOfForbiddenTags()
    {
        $post = Post::factory()->create(['body' => '<script>alert("Do something fishy.");</script>']);
        $this->assertStringNotContainsString('<script>', $post->body);
    }

    /** @test */
    public function testKnowsIfItIsTheBestPost()
    {
        $post = Post::factory()->create();

        $this->assertFalse($post->isBest());

        $post->thread->update(['best_post_id' => $post->id]);

        $this->assertTrue($post->fresh()->isBest());
    }

    /** @test */
    public function testBodyIsSanitizedAutomatically()
    {
        $post = Post::factory()->make(['body' => '<script>alert("bad");</script><p>This is okay.</p>']);

        $this->assertEquals($post->body, '<p>This is okay.</p>');
    }
}
