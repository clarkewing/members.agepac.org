<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Poll;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RestrictedChannelsTest extends TestCase
{
    /** @var \App\Models\Channel */
    public $channel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn()->withExceptionHandling();

        $this->channel = Channel::factory()->create(['name' => 'Foobar Channel']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotSeeThreadsFromARestrictedChannelInGeneralIndex()
    {
        $this->setupPermission('view');

        $unrestrictedThread = Thread::factory()->create();
        $restrictedThread = Thread::factory()->create(['channel_id' => $this->channel->id]);

        $threads = $this->indexThreads()
            ->assertSuccessful()
            ->json();

        $this->assertEquals(1, $threads['total']);

        $this->assertEquals($unrestrictedThread->id, $threads['data'][0]['id']);
    }

    /** @test */
    public function testAuthorizedUsersCanSeeThreadsFromARestrictedChannelInGeneralIndex()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('view');

        $unrestrictedThread = Thread::factory()->create();
        $restrictedThread = Thread::factory()->create(['channel_id' => $this->channel->id]);

        $threads = $this->indexThreads()
            ->assertSuccessful()
            ->json();

        $this->assertEquals(2, $threads['total']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotSeeRestrictedChannelsInSidebar()
    {
        $this->setupPermission('view');

        $this->get(route('threads.index'))
            ->assertDontSee($this->channel->name);
    }

    /** @test */
    public function testAuthorizedUsersSeeRestrictedChannelsInSidebar()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('view');

        $this->get(route('threads.index'))
            ->assertSee($this->channel->name);
    }

    /** @test */
    public function testUnauthorizedUsersCannotListThreadsInARestrictedChannel()
    {
        $this->setupPermission('view');

        $this->indexThreads($this->channel)
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanListThreadsInARestrictedChannel()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('view');

        $this->indexThreads($this->channel)
            ->assertSuccessful();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewAThreadInARestrictedChannel()
    {
        $this->setupPermission('view');

        $thread = Thread::factory()->create(['channel_id' => $this->channel->id]);

        $this->showThread($thread)
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanViewAThreadInARestrictedChannel()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('view');

        $thread = Thread::factory()->create(['channel_id' => $this->channel->id]);

        $this->showThread($thread)
            ->assertSuccessful();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewPostsFromAThreadInARestrictedChannel()
    {
        $this->setupPermission('view');

        $thread = Thread::factory()->create(['channel_id' => $this->channel->id]);

        $this->indexPosts($thread)
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanViewPostsFromAThreadInARestrictedChannel()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('view');

        $thread = Thread::factory()->create(['channel_id' => $this->channel->id]);

        $this->indexPosts($thread)
            ->assertSuccessful();
    }

    /** @test */
    public function testUnauthorizedUsersCannotCreateAThreadInARestrictedChannel()
    {
        $this->setupPermission('post');

        $this->createThread()
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanCreateAThreadInARestrictedChannel()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('post');

        $this->createThread()
            ->assertSuccessful();
    }

    /** @test */
    public function testUnauthorizedUsersCannotSeeRestrictedChannelsAsOptionsWhenCreatingAThread()
    {
        $this->setupPermission('post');

        $this->createThread()
            ->assertDontSee($this->channel->name);
    }

    /** @test */
    public function testAuthorizedUsersSeeRestrictedChannelsAsOptionsWhenCreatingAThread()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('post');

        $this->createThread()
            ->assertSee($this->channel->name);
    }

    /** @test */
    public function testUnauthorizedUsersCannotStoreAThreadInARestrictedChannel()
    {
        $this->setupPermission('post');

        $this->storeThread()
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanStoreAThreadInARestrictedChannel()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('post');

        $this->storeThread()
            ->assertSuccessful();
    }

    /** @test */
    public function testUnauthorizedUsersCannotReplyToAThreadInARestrictedChannel()
    {
        $this->setupPermission('post');

        $thread = Thread::factory()->create(['channel_id' => $this->channel->id]);

        $this->storePost($thread)
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanReplyToAThreadInARestrictedChannel()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('post');

        $thread = Thread::factory()->create(['channel_id' => $this->channel->id]);

        $this->storePost($thread)
            ->assertSuccessful();
    }

    /** @test */
    public function testUnauthorizedUsersCannotVoteInAPollInARestrictedChannel()
    {
        $this->setupPermission('vote');

        $poll = $this->createPoll();

        $this->voteInPoll($poll)
            ->assertForbidden();
    }

    /** @test */
    public function testAuthorizedUsersCanVoteInAPollInARestrictedChannel()
    {
        $this->signInWithRole('Administrator');

        $this->setupPermission('vote');

        $poll = $this->createPoll();

        $this->voteInPoll($poll)
            ->assertSuccessful();
    }

    /**
     * Setup a permission on the channel.
     *
     * @param  string  $permission
     */
    protected function setupPermission(string $permission): void
    {
        $this->channel->createPermission($permission);

        Role::findByName('Administrator')
            ->givePermissionTo($this->channel->{$permission . 'Permission'});
    }

    /**
     * @param  \App\Models\Channel|null  $channel
     * @return \Illuminate\Testing\TestResponse
     */
    protected function indexThreads(Channel $channel = null): TestResponse
    {
        return $this->getJson(route('threads.index', $channel));
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function showThread(Thread $thread): TestResponse
    {
        return $this->get(route('threads.show', [$thread->channel, $thread]));
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function indexPosts(Thread $thread): TestResponse
    {
        return $this->get(route('posts.index', [$thread->channel, $thread]));
    }

    /**
     * @return \Illuminate\Testing\TestResponse
     */
    protected function createThread(): TestResponse
    {
        return $this->get(route('threads.create', ['channel_id' => $this->channel->id]));
    }

    /**
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storeThread(): TestResponse
    {
        return $this->postJson(
            route('threads.store'),
            Thread::factory()->withBody()
                ->raw(['channel_id' => $this->channel->id])
        );
    }

    /**
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storePost(Thread $thread): TestResponse
    {
        return $this->postJson(
            $thread->path() . '/posts',
            Post::factory()->raw()
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    protected function createPoll()
    {
        return Poll::factory()->create(
            ['thread_id' => Thread::factory()->create(['channel_id' => $this->channel->id])->id]
        );
    }

    /**
     * @param  \App\Models\Poll  $poll
     * @return \Illuminate\Testing\TestResponse
     */
    protected function voteInPoll(Poll $poll): TestResponse
    {
        return $this->putJson(
            route('poll_votes.update', [$poll->thread->channel, $poll->thread]),
            ['vote' => (array) Arr::random($poll->options->pluck('id')->all())]
        );
    }
}
