<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoritesTest extends TestCase
{
    /**
     * @test
     */
    public function testGuestsCannotFavoriteAnyReply()
    {
        $this->withExceptionHandling()
            ->post('/replies/1/favorites')
            ->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function testAuthenticatedUserCanFavoriteAnyReply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->post('/replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }

    /**
     * @test
     */
    public function testAuthenticatedUserCanUnfavoriteAnyReply()
    {
        $this->signIn();

        $reply = create('App\Reply');
        $reply->favorite();

        $this->delete('/replies/' . $reply->id . '/favorites');
        $this->assertCount(0, $reply->favorites);
    }

    /**
     * @test
     */
    public function testAuthenticatedUserMayOnlyFavoriteAReplyOnce()
    {
        $this->signIn();

        $reply = create('App\Reply');

        try {
            $this->post('/replies/' . $reply->id . '/favorites');
            $this->post('/replies/' . $reply->id . '/favorites');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 19) { // SQLite UNIQUE constraint code
                $this->fail('Attempted to insert same record set twice.');
            }
            throw $e;
        }

        $this->assertCount(1, $reply->favorites);
    }
}
