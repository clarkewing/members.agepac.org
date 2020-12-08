<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function testUserCanFetchTheirMostRecentPost()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($post->id, $user->lastPost->id);
    }

    /** @test */
    public function testUserCanDetermineTheirAvatarPath()
    {
        $user = User::factory()->create();
        $this->assertTrue(Str::endsWith($user->avatar_path, 'images/avatars/default.jpg'));

        $user->avatar_path = 'avatars/me.jpg';
        $this->assertTrue(Str::endsWith($user->avatar_path, 'avatars/me.jpg'));
    }

    /** @test */
    public function testHasUsername()
    {
        $user = User::factory()->create(['username' => 'foo.bar']);

        $this->assertEquals('foo.bar', $user->username);
    }

    /** @test */
    public function testHasFirstName()
    {
        $user = User::factory()->create(['first_name' => 'John']);

        $this->assertEquals('John', $user->first_name);
    }

    /** @test */
    public function testHasLastName()
    {
        $user = User::factory()->create(['last_name' => 'Doe']);

        $this->assertEquals('Doe', $user->last_name);
    }

    /** @test */
    public function testKnowsItsFullName()
    {
        $user = User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);

        $this->assertEquals('John Doe', $user->name);
    }

    /** @test */
    public function testKnowsItsFullClass()
    {
        $user = User::factory()->create(['class_course' => 'EPL/S', 'class_year' => 2015]);

        $this->assertEquals('EPL/S 2015', $user->class);
    }

    /** @test */
    public function testCanMakeUsernameFromFirstAndLastName()
    {
        $this->assertEquals('john.doe', User::makeUsername('John', 'Doe'));
    }

    /** @test */
    public function testBirthdateCanBeNull()
    {
        $user = User::factory()->create(['birthdate' => null]);

        $this->assertNull($user->birthdate);
    }

    /** @test */
    public function testPhoneCanBeNull()
    {
        $user = User::factory()->create(['phone' => null]);

        $this->assertNull($user->phone);

        $user = User::factory()->create();

        $user->fill(['phone' => null])->save();

        $this->assertNull($user->phone);
    }
}
