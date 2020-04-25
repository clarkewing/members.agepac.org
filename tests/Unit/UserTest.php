<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function testUserCanFetchTheirMostRecentReply()
    {
        $user = create(User::class);

        $reply = create(Reply::class, ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    /**
     * @test
     */
    public function testUserCanDetermineTheirAvatarPath()
    {
        $user = create(User::class);
        $this->assertTrue(Str::endsWith($user->avatar_path, 'images/avatars/default.jpg'));

        $user->avatar_path = 'avatars/me.jpg';
        $this->assertTrue(Str::endsWith($user->avatar_path, 'avatars/me.jpg'));
    }
}
