<?php

namespace Tests\Feature;

use App\Channel;
use App\Post;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create(Thread::class);
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
        $this->signIn(create(User::class, ['username' => 'john.doe']));

        $threadByJohn = create(Thread::class, ['user_id' => Auth::id()]);
        $threadNotByJohn = create(Thread::class);

        $this->get(route('threads.index') . '?by=john.doe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByPopularity()
    {
        $threadWithTwoPosts = create(Thread::class);
        create(Post::class, ['thread_id' => $threadWithTwoPosts->id], 2);

        $threadWithThreePosts = create(Thread::class);
        create(Post::class, ['thread_id' => $threadWithThreePosts->id], 3);

        $threadWithNoPosts = $this->thread;

        $response = $this->getJson(route('threads.index') . '?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'posts_count'));
    }

    /** @test */
    public function testUserCanFilterThreadsByThoseThatAreUnanswered()
    {
        $threadWithNoPosts = $this->thread;

        $threadWithPost = create(Thread::class);
        create(Post::class, ['thread_id' => $threadWithPost->id]);

        $response = $this->getJson(route('threads.index') . '?unanswered=1')->json();

        $this->assertEquals([$threadWithNoPosts->id], array_column($response['data'], 'id'));
    }

    /** @test */
    public function testUserCanRequestAllPostsForAGivenThread()
    {
        create(Post::class, ['thread_id' => $this->thread->id], 2);

        $response = $this->getJson($this->thread->path() . '/posts')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }

    /** @test */
    public function testRecordsNewVisitEachTimeThreadIsRead()
    {
        $thread = create(Thread::class);
        $thread->visits()->reset();

        $this->assertSame(0, $thread->visits()->count());

        $this->get($thread->path());

        $this->assertEquals(1, $thread->visits()->count());
    }
}
