<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function testGuestCannotIndexThreads()
    {
        Auth::logout();

        $this->get(route('threads.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUserCanIndexThreads()
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
    public function testUserCannotViewADeletedThread()
    {
        $this->thread->delete();

        $this->get($this->thread->path())
            ->assertNotFound();
    }

    /** @test */
    public function testUserCanViewListOfChannelsGroupedByParentOnThreadsIndex()
    {
        $parentChannel = Channel::factory()->create();
        $childChannel = Channel::factory()->create(['parent_id' => $parentChannel->id]);

        $this->get(route('threads.index'))
            ->assertSuccessful()
            ->assertSeeInOrder([$parentChannel->name, $childChannel->name]);

        // TODO: Improve seeInOrder assertion which passes in other order too...
    }

    /** @test */
    public function testUserCanFilterThreadsByChannel()
    {
        $channel = Channel::factory()->create();
        $threadInChannel = Thread::factory()->create(['channel_id' => $channel->id]);
        $threadNotInChannel = Thread::factory()->create();

        $this->get(route('threads.index', $channel))
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByArchivedChannel()
    {
        $channel = Channel::factory()->create(['archived' => true]);
        $thread = Thread::factory()->create(['channel_id' => $channel->id]);

        $this->get(route('threads.index', $channel))
            ->assertSee($thread->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByUsername()
    {
        $threadByUser = Thread::factory()->create(['user_id' => Auth::id()]);
        $threadNotByUser = Thread::factory()->create();

        $this->get(route('threads.index').'?by='.Auth::user()->username)
            ->assertSee($threadByUser->title)
            ->assertDontSee($threadNotByUser->title);
    }

    /** @test */
    public function testUserCanFilterThreadsByPopularity()
    {
        $threadWithTwoReplies = Thread::factory()->create();
        Post::factory()->count(2)->create(['thread_id' => $threadWithTwoReplies->id]);

        $threadWithThreeReplies = Thread::factory()->create();
        Post::factory()->count(3)->create(['thread_id' => $threadWithThreeReplies->id]);

        $threadWithNoPosts = $this->thread;

        $response = $this->getJson(route('threads.index').'?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /** @test */
    public function testUserCanFilterThreadsByThoseThatAreUnanswered()
    {
        $threadWithNoReplies = $this->thread;

        $threadWithReply = Thread::factory()->create();
        Post::factory()->create(['thread_id' => $threadWithReply->id]);

        $response = $this->getJson(route('threads.index').'?unanswered=1')->json();

        $this->assertEquals([$threadWithNoReplies->id], array_column($response['data'], 'id'));
    }

    /** @test */
    public function testUserCanRequestAllPostsForAGivenThread()
    {
        Post::factory()->count(2)->create(['thread_id' => $this->thread->id]);

        $response = $this->getJson($this->thread->path().'/posts')->json();

        // Thread initiator post + 2 posts.
        $this->assertCount(1 + 2, $response['data']);
        $this->assertEquals(1 + 2, $response['total']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotSeeDeletedPosts()
    {
        Post::factory()->create(['thread_id' => $this->thread->id])->delete();

        $response = $this->getJson($this->thread->path().'/posts')->json();

        // Just thread initiator post.
        $this->assertCount(1, $response['data']);
        $this->assertEquals(1, $response['total']);
    }

    /** @test */
    public function testAuthorizedUsersCanSeeDeletedPosts()
    {
        $this->signInWithPermission('posts.viewDeleted');

        Post::factory()->create(['thread_id' => $this->thread->id])->delete();

        $response = $this->getJson($this->thread->path().'/posts')->json();

        // Thread initiator post + 1 deleted post.
        $this->assertCount(1 + 1, $response['data']);
        $this->assertEquals(1 + 1, $response['total']);
    }

    /** @test */
    public function testRecordsNewVisitEachTimeThreadIsRead()
    {
        $thread = Thread::factory()->create();

        $this->assertSame(0, $thread->visits);

        $this->get($thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }
}
