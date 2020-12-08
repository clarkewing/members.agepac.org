<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    /** @test */
    public function testGuestsCannotFavoriteAnyPost()
    {
        $this->withExceptionHandling()
            ->post(route('posts.favorite', 1))
            ->assertRedirect('/login');
    }

    /** @test */
    public function testAuthenticatedUserCanFavoriteAnyPost()
    {
        $this->signIn();

        $post = \App\Models\Post::factory()->create();

        $this->post(route('posts.favorite', $post));

        $this->assertCount(1, $post->favorites);
    }

    /** @test */
    public function testAuthenticatedUserCanUnfavoriteAnyPost()
    {
        $this->signIn();

        $post = \App\Models\Post::factory()->create();
        $post->favorite();

        $this->delete(route('posts.unfavorite', $post));
        $this->assertCount(0, $post->favorites);
    }

    /** @test */
    public function testAuthenticatedUserMayOnlyFavoriteAPostOnce()
    {
        $this->signIn();

        $post = \App\Models\Post::factory()->create();

        try {
            $this->post(route('posts.favorite', $post));
            $this->post(route('posts.favorite', $post));
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 19) { // SQLite UNIQUE constraint code
                $this->fail('Attempted to insert same record set twice.');
            }
            throw $e;
        }

        $this->assertCount(1, $post->favorites);
    }
}
