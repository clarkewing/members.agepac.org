<?php

namespace Tests\Unit;

use App\Post;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function testUserCanFetchTheirMostRecentPost()
    {
        $user = create(User::class);

        $post = create(Post::class, ['user_id' => $user->id]);

        $this->assertEquals($post->id, $user->lastPost->id);
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

    /** @test */
    public function testCanHaveBio()
    {
        $user = create(User::class, ['bio' => 'This is a pretty awesome bio.']);

        $this->assertEquals('This is a pretty awesome bio.', $user->bio);
    }

    /** @test */
    public function testCanHaveFlightHours()
    {
        $user = create(User::class, ['flight_hours' => 150]);

        $this->assertSame(150, $user->flight_hours);
    }
}
