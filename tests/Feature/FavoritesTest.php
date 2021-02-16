<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    protected $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->post = Post::factory()->create();
    }

    /** @test */
    public function testGuestsCannotFavoriteAnyPost()
    {
        Auth::logout();

        $this->favoritePost()
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotFavoriteAnyPost()
    {
        $this->signInUnsubscribed();

        $this->favoritePost()
            ->assertPaymentRequired();
    }

    /** @test */
    public function testAuthenticatedUserCanFavoriteAnyPost()
    {
        $this->favoritePost();

        $this->assertCount(1, $this->post->favorites);
    }

    /** @test */
    public function testAuthenticatedUserCanUnfavoriteAnyPost()
    {
        $this->post->favorite();

        $this->unfavoritePost();

        $this->assertCount(0, $this->post->favorites);
    }

    /** @test */
    public function testAuthenticatedUserMayOnlyFavoriteAPostOnce()
    {
        $this->withoutExceptionHandling();

        try {
            $this->favoritePost();
            $this->favoritePost();
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 19) { // SQLite UNIQUE constraint code
                $this->fail('Attempted to insert same record set twice.');
            }
            throw $e;
        }

        $this->assertCount(1, $this->post->favorites);
    }

    /** @test */
    public function testGuestsCannotSeePostFavorites()
    {
        Auth::logout();

        $this->getJson(route('posts.favorites', $this->post))
            ->assertUnauthorized();
    }

    /** @test */
    public function testUnsubscribedUsersCannotSeePostFavorites()
    {
        $this->signInUnsubscribed();

        $this->getJson(route('posts.favorites', $this->post))
            ->assertPaymentRequired();
    }

    /** @test */
    public function testAuthenticatedUserCanSeePostFavorites()
    {
        $this->getJson(route('posts.favorites', $this->post))
            ->assertJson([]);

        $this->post->favorite(User::factory()->create()->id);

        $this->getJson(route('posts.favorites', $this->post))
            ->assertJson($this->post->favorites->pluck('owner.username')->all());
    }

    /**
     * @return \Illuminate\Testing\TestResponse
     */
    protected function favoritePost(): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('posts.favorite', $this->post));
    }

    /**
     * @return \Illuminate\Testing\TestResponse
     */
    protected function unfavoritePost(): \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(route('posts.unfavorite', $this->post));
    }
}
