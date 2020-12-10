<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    /** @test */
    public function testMentionedUsersInAPostAreNotified()
    {
        $john = User::factory()->create(['username' => 'john.doe']);

        $this->signIn($john);

        $jane = User::factory()->create(['username' => 'jane.doe']);

        $thread = Thread::factory()->create();

        $this->postJson($thread->path() . '/posts', Post::factory()->raw([
            'body' => 'Hey @jane.doe look at this!',
        ]));

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function testMentionedUsersInAThreadAreNotified()
    {
        $john = User::factory()->create(['username' => 'john.doe']);

        $this->signIn($john);

        $jane = User::factory()->create(['username' => 'jane.doe']);

        $this->postJson(route('threads.store'), Thread::factory()->make([
            'body' => 'Hey @jane.doe look at this!',
        ])->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function testCanFetchUsersMatchingSearch()
    {
        User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        User::factory()->create(['first_name' => 'John', 'last_name' => 'Smith']);
        User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']);

        $this->getJson(route('api.users.index', ['name' => 'john']))
            ->assertJsonCount(2);

        $this->getJson(route('api.users.index', ['name' => 'smith']))
            ->assertJsonCount(1);
    }

    /** @test */
    public function testMatchesMentionAtBeginning()
    {
        User::factory()->create(['username' => 'john.doe']);

        $post = new Post(['body' => '@john.doe']);

        $this->assertEquals('<a href="/profiles/john.doe">@john.doe</a>', $post->body);
    }

    /** @test */
    public function testMatchesMentionInText()
    {
        User::factory()->create(['username' => 'john.doe']);

        $post = new Post(['body' => 'Hey @john.doe, you\'re pretty cool.']);

        $this->assertEquals('Hey <a href="/profiles/john.doe">@john.doe</a>, you\'re pretty cool.', $post->body);
    }

    /** @test */
    public function testOnlyMatchesExistingUsers()
    {
        $username = 'john.doe';
        $body = "@$username";

        $post = new Post(compact('body'));
        $this->assertEquals($body, $post->body);

        User::factory()->create(['username' => $username]);

        $post = new Post(compact('body'));
        $this->assertEquals("<a href=\"/profiles/$username\">@$username</a>", $post->body);
    }

    /** @test */
    public function testDoesntMatchSimilarPatterns()
    {
        $body = 'easyJet, et non Easyjet, ou e@syj3t…';
        $post = new Post(compact('body'));

        $this->assertEquals($body, $post->body);
    }

    /** @test */
    public function testDoesntMatchEmails()
    {
        $body = 'julien_regnault@yahoo.com';
        $post = new Post(compact('body'));

        $this->assertEquals($body, $post->body);
    }

    /** @test */
    public function testDoesntMatchLooseEmailsDomains()
    {
        $body = '@yahoo.com';
        $post = new Post(compact('body'));

        $this->assertEquals($body, $post->body);
    }
}
