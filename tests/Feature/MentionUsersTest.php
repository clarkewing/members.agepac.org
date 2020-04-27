<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    /**
     * @test
     */
    public function testMentionedUsersInAReplyAreNotified()
    {
        $john = create(User::class, ['name' => 'JohnDoe']);

        $this->signIn($john);

        $jane = create(User::class, ['name' => 'JaneDoe']);

        $thread = create(Thread::class);

        $reply = make(Reply::class, [
            'body' => 'Hey @JaneDoe look at this!',
        ]);

        $this->postJson($thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /**
     * @test
     */
    public function testCanFetchAllMentionedUsersStartingWithGivenCharacters()
    {
        create(User::class, ['name' => 'JohnDoe']);
        create(User::class, ['name' => 'JohnDoe2']);
        create(User::class, ['name' => 'JaneDoe']);

        $results = $this->getJson(route('api.users.index', [
            'name' => 'john',
        ]));

        $results->assertJsonCount(2);
    }
}
