<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    /** @test */
    public function testGuestsCannotFavoriteAnyReply()
    {
        $this->withExceptionHandling()
            ->post(route('replies.favorite', 1))
            ->assertRedirect('/login');
    }

    /** @test */
    public function testAuthenticatedUserCanFavoriteAnyReply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->post(route('replies.favorite', $reply));

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function testAuthenticatedUserCanUnfavoriteAnyReply()
    {
        $this->signIn();

        $reply = create('App\Reply');
        $reply->favorite();

        $this->delete(route('replies.unfavorite', $reply));
        $this->assertCount(0, $reply->favorites);
    }

    /** @test */
    public function testAuthenticatedUserMayOnlyFavoriteAReplyOnce()
    {
        $this->signIn();

        $reply = create('App\Reply');

        try {
            $this->post(route('replies.favorite', $reply));
            $this->post(route('replies.favorite', $reply));
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 19) { // SQLite UNIQUE constraint code
                $this->fail('Attempted to insert same record set twice.');
            }
            throw $e;
        }

        $this->assertCount(1, $reply->favorites);
    }
}
