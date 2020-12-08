<?php

namespace Tests\Unit;

use App\Channel;
use App\Notifications\ThreadWasUpdated;
use App\Post;
use App\Thread;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function testHasAPath()
    {
        $this->assertEquals(
            route('threads.show', [$this->thread->channel, $this->thread]),
            $this->thread->path()
        );
    }

    /** @test */
    public function testPathUnaffectedByArchivedChannel()
    {
        $thread = Thread::factory()->create();
        $path = $thread->path();

        $thread->channel->archive();

        $this->assertEquals($path, $thread->fresh()->path());
    }

    /** @test */
    public function testHasACreator()
    {
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    /** @test */
    public function testHasPosts()
    {
        $this->assertCount(1, $this->thread->posts);
        $this->assertInstanceOf(Collection::class, $this->thread->posts);

        Post::factory()->create(['thread_id' => $this->thread->id]);

        $this->assertCount(2, $this->thread->fresh()->posts);
        $this->assertInstanceOf(Collection::class, $this->thread->fresh()->posts);
    }

    /** @test */
    public function testHasInitiatorPost()
    {
        $this->assertInstanceOf(Post::class, $this->thread->initiatorPost);
    }

    /** @test */
    public function testHasReplies()
    {
        $this->assertCount(0, $this->thread->replies);
        $this->assertInstanceOf(Collection::class, $this->thread->replies);

        Post::factory()->create(['thread_id' => $this->thread->id]);

        $this->assertCount(1, $this->thread->fresh()->replies);
        $this->assertInstanceOf(Collection::class, $this->thread->fresh()->replies);
    }

    /** @test */
    public function testCanAddAPost()
    {
        // There should already be the thread initiator post.
        $this->assertCount(1, $this->thread->posts);

        $this->thread->addPost([
            'body' => 'Foobar',
            'user_id' => 1,
        ]);

        $this->assertCount(2, $this->thread->fresh()->posts);
    }

    /** @test */
    public function testNotifiesAllSubscribersWhenAPostIsAdded()
    {
        Notification::fake();

        $this->signIn()
            ->thread
            ->subscribe()
            ->addPost([
                'body' => 'Foobar',
                'user_id' => User::factory()->create()->id,
            ]);

        Notification::assertSentTo(Auth::user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function testBelongsToAChannel()
    {
        $this->assertInstanceOf(Channel::class, $this->thread->channel);
    }

    /** @test */
    public function testCanBeSubscribedTo()
    {
        $this->thread->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $this->thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /** @test */
    public function testCanBeUnsubscribedFrom()
    {
        $this->thread->subscribe($userId = 1);
        $this->thread->unsubscribe($userId = 1);

        $this->assertEquals(
            0,
            $this->thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /** @test */
    public function testKnowsIfAuthenticatedUserIsSubscribedToIt()
    {
        $this->signIn();

        $this->assertFalse($this->thread->isSubscribedTo);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);
    }

    /** @test */
    public function testCanCheckIfTheAuthenticatedUserHasReadAllPosts()
    {
        $this->signIn($user = User::factory()->create());

        $this->assertTrue($this->thread->hasUpdatesFor($user));

        $user->read($this->thread);

        $this->assertFalse($this->thread->hasUpdatesFor($user));
    }

    /** @test */
    public function testBodyIsSanitizedAutomatically()
    {
        $thread = Thread::factory()->make(['body' => '<script>alert("bad");</script><p>This is okay.</p>']);

        $this->assertEquals($thread->body, '<p>This is okay.</p>');
    }

    /** @test */
    public function testCanHaveABestPost()
    {
        $post = Post::factory()->create(['thread_id' => $this->thread->id]);

        $this->thread->markBestPost($post);

        $this->assertEquals($post->id, $this->thread->bestPost->id);
    }
}
