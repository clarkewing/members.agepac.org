<?php

namespace Tests\Feature;

use App\Channel;
use App\Post;
use App\Thread;
use App\User;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    /** @test */
    public function testGuestCannotCreateThread()
    {
        $this->withExceptionHandling();

        $this->get(route('threads.create'))
            ->assertRedirect(route('login'));

        $this->post(route('threads.store'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testNewUserMustFirstVerifyEmailBeforeCreatingThreads()
    {
        $this->signIn(
            factory(User::class)->states('unverified_email')->create()
        );

        $this->get(route('threads.create'))
            ->assertRedirect(route('threads.index'))
            ->assertSessionHas('flash', 'Tu dois vérifier ton adresse email avant de pouvoir publier.');

        $thread = make(Thread::class);

        $this->post(route('threads.store'), $thread->toArray())
            ->assertRedirect(route('threads.index'))
            ->assertSessionHas('flash', 'Tu dois vérifier ton adresse email avant de pouvoir publier.');
    }

    /** @test */
    public function testUserCanCreateNewThreads()
    {
        $this->followingRedirects()
            ->publishThread(['title' => 'Some title', 'body' => 'This is the body.'])
            ->assertSee('Some title');

        $this->assertDatabaseHas('posts', ['body' => 'This is the body.']);
    }

    /** @test */
    public function testNewThreadCreatesAThreadInitiatorPost()
    {
        $this->publishThread(['title' => 'Some title', 'body' => 'This is the body.']);

        $this->assertDatabaseHas('threads', ['title' => 'Some title']);
        $this->assertDatabaseHas('posts', [
            'body' => 'This is the body.',
            'is_thread_initiator' => true,
        ]);
    }

    /** @test */
    public function testThreadRequiresATitle()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function testThreadRequiresABody()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function testThreadRequiresAValidChannel()
    {
        factory(Channel::class, 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function testThreadCantBeCreatedInAnArchivedChannel()
    {
        $archivedChannel = create(Channel::class, ['archived' => true]);

        $this->publishThread(['channel_id' => $archivedChannel->id])
            ->assertSessionHasErrors('channel_id');

        $this->assertCount(0, $archivedChannel->threads);
    }

    /** @test */
    public function testThreadRequiresAUniqueSlug()
    {
        $this->signIn();

        $existingThread = create(Thread::class, ['title' => 'Foo Title']);

        $this->assertEquals('foo-title', $existingThread->fresh()->slug);

        $thread = $this->publishThread(['title' => $existingThread->title], true)->json();

        $this->assertEquals('foo-title-'.strtotime($thread['created_at']), $thread['slug']);
    }

    /** @test */
    public function testThreadWithATitleEndingInANumberShouldGenerateTheProperSlug()
    {
        $this->signIn();

        $existingThread = create(Thread::class, ['title' => 'Financials 2020']);

        $thread = $this->publishThread(['title' => $existingThread->title], true)->json();

        $this->assertEquals('financials-2020-'.strtotime($thread['created_at']), $thread['slug']);
    }

    /**
     * Submits a post request to publish a thread.
     *
     * @param  array  $overrides
     * @param  bool  $wantsJson
     * @return \Illuminate\Testing\TestResponse
     */
    public function publishThread(array $overrides = [], bool $wantsJson = false)
    {
        $this->withExceptionHandling()->signIn();

        $thread = factory(Thread::class)->states('with_body')->make($overrides);

        if ($wantsJson) {
            return $this->postJson(route('threads.store'), $thread->toArray());
        }

        return $this->post(route('threads.store'), $thread->toArray());
    }
}
