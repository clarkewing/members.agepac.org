<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Reputation;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ReputationTest extends TestCase
{
    /**
     * @var array Reputation points
     */
    protected $points;

    /**
     * Fetch current reputation points on class initialization.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->points = config('council.reputation');
    }

    /** @test */
    public function testUserEarnsPointsWhenCreatingAThread()
    {
        $thread = Thread::factory()->create();

        $this->assertEquals($this->points['thread_published'], $thread->creator->reputation);
    }

    /** @test */
    public function testUserLosesPointsWhenTheirThreadIsDeleted()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['user_id' => Auth::id()]);

        $this->assertEquals($this->points['thread_published'], $thread->creator->reputation);

        $this->delete($thread->path());

        $this->assertEquals(0, $thread->creator->fresh()->reputation);
    }

    /** @test */
    public function testUserEarnsPointsWhenPostingOnAThread()
    {
        $thread = Thread::factory()->create();

        $post = $thread->addPost([
            'user_id' => User::factory()->create()->id,
            'body' => 'Here is a post.',
        ]);

        $this->assertEquals($this->points['reply_posted'], $post->owner->reputation);
    }

    /** @test */
    public function testUserLosesPointsWhenTheirPostOnAThreadIsDeleted()
    {
        $this->signIn();

        $post = Post::factory()->create(['user_id' => Auth::id()]);

        $this->assertEquals($this->points['reply_posted'], $post->owner->reputation);

        $this->delete(route('posts.destroy', $post));

        $this->assertEquals(0, $post->owner->fresh()->reputation);
    }

    /** @test */
    public function testUserEarnsPointsWhenTheirPostIsMarkedAsBest()
    {
        $thread = Thread::factory()->create();

        $thread->markBestPost($post = $thread->addPost([
            'user_id' => User::factory()->create()->id,
            'body' => 'Here is a post.',
        ]));

        $total = $this->points['reply_posted'] + $this->points['best_post_awarded'];
        $this->assertEquals($total, $post->owner->reputation);
    }

    public function testOnBestPostChangeThePointsShouldBeTransferred()
    {
        $thread = Thread::factory()->create();
        [$firstPost, $secondPost] = Post::factory()->count(2)->create();

        $thread->markBestPost($firstPost);

        $total = $this->points['reply_posted'] + $this->points['best_post_awarded'];
        $this->assertEquals($total, $firstPost->owner->reputation);

        // If the owner of the thread decides to choose a different best post...
        $thread->markBestPost($secondPost);

        // Then the original recipient of the best post reputation should be stripped of those points.
        $total = $this->points['reply_posted'] + $this->points['best_post_awarded'] - $this->points['best_post_awarded'];
        $this->assertEquals($total, $firstPost->owner->fresh()->reputation);

        // And those points should now be reflected on the account of the new best post owner.
        $total = $this->points['reply_posted'] + $this->points['best_post_awarded'];
        $this->assertEquals($total, $secondPost->owner->reputation);
    }

    public function testUserLosesPointsWhenTheirBestPostIsUnmarked()
    {
        $post = Post::factory()->create();
        $thread = $post->thread;

        $thread->markBestPost($post);

        $total = $this->points['reply_posted'] + $this->points['best_post_awarded'];
        $this->assertEquals($total, $post->owner->reputation);

        $thread->unmarkBestPost($post);

        $total = $this->points['reply_posted'] + $this->points['best_post_awarded'] - $this->points['best_post_awarded'];
        $this->assertEquals($total, $post->owner->fresh()->reputation);
    }

    public function testUserLosesPointsWhenTheirBestPostIsDeleted()
    {
        $post = Post::factory()->create();
        $thread = $post->thread;

        $thread->markBestPost($post);

        $total = $this->points['reply_posted'] + $this->points['best_post_awarded'];
        $this->assertEquals($total, $post->owner->reputation);

        $post->delete();

        $this->assertEquals(0, $post->owner->fresh()->reputation);
    }

    /** @test */
    public function testUserEarnsPointsWhenTheirPostIsFavorited()
    {
        $this->signIn();

        $post = Post::factory()->create();

        $post->favorite();

        $total = $this->points['reply_posted'] + $this->points['post_favorited'];
        $this->assertEquals($total, $post->owner->fresh()->reputation);
    }

    /** @test */
    public function testUserLosesPointsWhenTheirPostIsUnfavorited()
    {
        $post = Post::factory()->create();

        $this->signIn();

        $post->favorite();

        $total = $this->points['reply_posted'] + $this->points['post_favorited'];
        $this->assertEquals($total, $post->owner->fresh()->reputation);

        $post->unfavorite();

        $total = $this->points['reply_posted'] + $this->points['post_favorited'] - $this->points['post_favorited'];
        $this->assertEquals($total, $post->owner->fresh()->reputation);

        $this->assertEquals(0, Auth::user()->reputation);
    }

    /** @test */
    public function testUserCanHaveTheirReputationReset()
    {
        $user = User::factory()->create(['reputation' => 100]);

        $this->assertEquals(100, $user->reputation);

        $user->resetReputation();

        $this->assertEquals(0, $user->reputation);
    }
}
