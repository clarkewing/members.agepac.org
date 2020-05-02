<?php

namespace Tests\Feature;

use App\Channel;
use App\Reply;
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
    public function testUserCanFilterThreadsByUsername()
    {
        $this->signIn(create(User::class, ['name' => 'JohnDoe']));

        $threadByJohn = create(Thread::class, ['user_id' => Auth::id()]);
        $threadNotByJohn = create(Thread::class);

        $this->get(route('threads.index') . '?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByPopularity()
    {
        $threadWithTwoReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson(route('threads.index') . '?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /** @test */
    public function testUserCanFilterThreadsByThoseThatAreUnanswered()
    {
        $threadWithNoReplies = $this->thread;

        $threadWithReply = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithReply->id]);

        $response = $this->getJson(route('threads.index') . '?unanswered=1')->json();

        $this->assertEquals([$threadWithNoReplies->id], array_column($response['data'], 'id'));
    }

    /** @test */
    public function testUserCanRequestAllRepliesForAGivenThread()
    {
        create(Reply::class, ['thread_id' => $this->thread->id], 2);

        $response = $this->getJson($this->thread->path() . '/replies')->json();

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
