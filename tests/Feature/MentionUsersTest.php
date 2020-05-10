<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    /** @test */
    public function testMentionedUsersInAReplyAreNotified()
    {
        $john = create(User::class, ['username' => 'john.doe']);

        $this->signIn($john);

        $jane = create(User::class, ['username' => 'jane.doe']);

        $thread = create(Thread::class);

        $reply = make(Reply::class, [
            'body' => 'Hey @jane.doe look at this!',
        ]);

        $this->postJson($thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function testMentionedUsersInAThreadAreNotified()
    {
        $john = create(User::class, ['username' => 'john.doe']);

        $this->signIn($john);

        $jane = create(User::class, ['username' => 'jane.doe']);

        $this->postJson(route('threads.store'), make(Thread::class, [
            'body' => 'Hey @jane.doe look at this!',
        ])->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function testCanFetchUsersMatchingSearch()
    {
        create(User::class, ['first_name' => 'John', 'last_name' => 'Doe']);
        create(User::class, ['first_name' => 'John', 'last_name' => 'Smith']);
        create(User::class, ['first_name' => 'Jane', 'last_name' => 'Doe']);

        $this->getJson(route('api.users.index', ['name' => 'john']))
            ->assertJsonCount(2);

        $this->getJson(route('api.users.index', ['name' => 'smith']))
            ->assertJsonCount(1);
    }
}
