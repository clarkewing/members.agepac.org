<?php

namespace Tests\Feature;

use App\User;
use App\Reply;
use App\Thread;
use App\Channel;
use App\Activity;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class CreateThreadsTest extends TestCase
{
    /**
     * @test
     */
    public function testGuestCannotCreateThread()
    {
        $this->withExceptionHandling();

        $this->get(route('threads.create'))
            ->assertRedirect(route('login'));

        $this->post(route('threads'))
            ->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function testNewUserMustFirstVerifyEmailBeforeCreatingThreads()
    {
        $this->signIn(
            factory(User::class)->states('unverified_email')->create()
        );

        $this->get(route('threads.create'))
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'Tu dois vérifier ton adresse email avant de pouvoir publier.');

        $thread = make(Thread::class);

        $this->post(route('threads'), $thread->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'Tu dois vérifier ton adresse email avant de pouvoir publier.');
    }

    /**
     * @test
     */
    public function testUserCanCreateNewThreads()
    {
        $this->followingRedirects()
            ->publishThread(['title' => 'Some title', 'body' => 'This is the body.'])
            ->assertSee('Some title')
            ->assertSee('This is the body.');
    }

    /**
     * @test
     */
    public function testThreadRequiresATitle()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /**
     * @test
     */
    public function testThreadRequiresABody()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /**
     * @test
     */
    public function testThreadRequiresAValidChannel()
    {
        factory(Channel::class, 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /**
     * @test
     */
    public function testThreadRequiresAUniqueSlug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'Foo Title']);

        $this->assertEquals($thread->fresh()->slug, 'foo-title');

        $thread = $this->postJson(route('threads'), $thread->toArray())->json();

        $this->assertEquals('foo-title-'.strtotime($thread['created_at']), $thread['slug']);
    }

    /**
     * @test
     */
    public function testThreadWithATitleEndingInANumberShouldGenerateTheProperSlug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'Financials 2020']);

        $thread = $this->postJson(route('threads'), $thread->toArray())->json();

        $this->assertEquals('financials-2020-'.strtotime($thread['created_at']), $thread['slug']);
    }

    /**
     * @test
     */
    public function testUnauthorizedUsersMayNotDeleteThreads()
    {
        $this->withExceptionHandling();

        $thread = create(Thread::class);

        $this->delete($thread->path())
            ->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function testAuthorizedUsersCanDeleteThreads()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);
        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $this->json('DELETE', $thread->path())
            ->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, Activity::count());
    }

    /**
     * Submits a post request to publish a thread.
     *
     * @param  array $overrides
     * @return $this;
     */
    public function publishThread(array $overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make(Thread::class, $overrides);

        return $this->post(route('threads'), $thread->toArray());
    }
}
