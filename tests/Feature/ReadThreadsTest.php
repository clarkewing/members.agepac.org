<?php

namespace Tests\Feature;

use App\Channel;
use App\Post;
use App\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = create(Thread::class);
    }

    /** @test */
    public function testGuestCannotViewAllThreads()
    {
        Auth::logout();

        $this->get(route('threads.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUserCanViewAllThreads()
    {
        $this->get(route('threads.index'))
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function testUserCanViewASingleThread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function testUserCanViewAThreadFromAnArchivedChannel()
    {
        $this->thread->channel->archive();

        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByChannel()
    {
        $channel = create(Channel::class);
        $threadInChannel = create(Thread::class, ['channel_id' => $channel->id]);
        $threadNotInChannel = create(Thread::class);

        $this->get(route('threads.index', $channel))
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByArchivedChannel()
    {
        $channel = create(Channel::class, ['archived' => true]);
        $thread = create(Thread::class, ['channel_id' => $channel->id]);

        $this->get(route('threads.index', $channel))
            ->assertSee($thread->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByUsername()
    {
        $threadByUser = create(Thread::class, ['user_id' => Auth::id()]);
        $threadNotByUser = create(Thread::class);

        $this->get(route('threads.index') . '?by=' . Auth::user()->username)
            ->assertSee($threadByUser->title)
            ->assertDontSee($threadNotByUser->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByPopularity()
    {
        $threadWithTwoReplies = create(Thread::class);
        create(Post::class, ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create(Thread::class);
        create(Post::class, ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithNoPosts = $this->thread;

        $response = $this->getJson(route('threads.index') . '?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /** @test */
    public function testUserCanFilterThreadsByThoseThatAreUnanswered()
    {
        $threadWithNoReplies = $this->thread;

        $threadWithReply = create(Thread::class);
        create(Post::class, ['thread_id' => $threadWithReply->id]);

        $response = $this->getJson(route('threads.index') . '?unanswered=1')->json();

        $this->assertEquals([$threadWithNoReplies->id], array_column($response['data'], 'id'));
    }

    /** @test */
    public function testUserCanRequestAllPostsForAGivenThread()
    {
        create(Post::class, ['thread_id' => $this->thread->id], 2);

        $response = $this->getJson($this->thread->path() . '/posts')->json();

        // Thread initiator post + 2 posts.
        $this->assertCount(1 + 2, $response['data']);
        $this->assertEquals(1 + 2, $response['total']);
    }

    /** @test */
    public function testRecordsNewVisitEachTimeThreadIsRead()
    {
        $thread = create(Thread::class);

        $this->assertSame(0, $thread->visits);

        $this->get($thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }
}
