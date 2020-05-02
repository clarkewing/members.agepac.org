<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function testUserCanFetchTheirMostRecentReply()
    {
        $user = create(User::class);

        $reply = create(Reply::class, ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    /** @test */
    public function testUserCanDetermineTheirAvatarPath()
    {
        $user = create(User::class);
        $this->assertTrue(Str::endsWith($user->avatar_path, 'images/avatars/default.jpg'));

        $user->avatar_path = 'avatars/me.jpg';
        $this->assertTrue(Str::endsWith($user->avatar_path, 'avatars/me.jpg'));
    }

    /** @test */
    public function testHasUsername()
    {
        $user = create(User::class, ['username' => 'foo.bar']);

        $this->assertEquals('foo.bar', $user->username);
    }

    /** @test */
    public function testHasFirstName()
    {
        $user = create(User::class, ['first_name' => 'John']);

        $this->assertEquals('John', $user->first_name);
    }

    /** @test */
    public function testHasLastName()
    {
        $user = create(User::class, ['last_name' => 'Doe']);

        $this->assertEquals('Doe', $user->last_name);
    }

    /** @test */
    public function testKnowsItsFullName()
    {
        $user = create(User::class, ['first_name' => 'John', 'last_name' => 'Doe']);

        $this->assertEquals('John Doe', $user->name);
    }

    /** @test */
    public function testCanMakeUsernameFromFirstAndLastName()
    {
        $this->assertEquals('john.doe', User::makeUsername('John', 'Doe'));
    }
}
