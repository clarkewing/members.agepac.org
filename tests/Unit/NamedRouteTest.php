<?php

namespace Tests\Unit;

use App\Channel;
use App\Thread;
use Tests\TestCase;

class NamedRouteTest extends TestCase
{
    /* @test */
    public function testHome()
    {
        $this->assertRoutePathIs('/home', 'home');
    }

    /* @test */
    public function testThreadIndex()
    {
        $this->assertRoutePathIs('/threads', 'threads.index');
    }

    /* @test */
    public function testThreadIndexWithChannel()
    {
        $channel = make(Channel::class);

        $this->assertRoutePathIs("/threads/{$channel->slug}", 'threads.index', $channel);
    }

    /* @test */
    public function testThreadCreate()
    {
        $this->assertRoutePathIs('/threads/create', 'threads.create');
    }

    /* @test */
    public function testThreadStore()
    {
        $this->assertRoutePathIs('/threads', 'threads.store');
    }

    /* @test */
    public function testThreadSearch()
    {
        $this->assertRoutePathIs('/threads/search', 'threads.search');
    }

    /* @test */
    public function testThreadShow()
    {
        $thread = create(Thread::class); // Create required to generate slug

        $this->assertRoutePathIs(
            "/threads/{$thread->channel->slug}/{$thread->slug}",
            'threads.show', [$thread->channel, $thread]
        );
    }

    /* @test */
    public function testThreadUpdate()
    {
        $thread = create(Thread::class);

        $this->assertRoutePathIs(
            "/threads/{$thread->channel->slug}/{$thread->slug}",
            'threads.update', [$thread->channel, $thread]
        );
    }

    /* @test */
    public function testThreadDestroy()
    {
        $thread = create(Thread::class);

        $this->assertRoutePathIs(
            "/threads/{$thread->channel->slug}/{$thread->slug}",
            'threads.destroy', [$thread->channel, $thread]
        );
    }


    public function assertRoutePathIs(string $expectedPath, string $routeName, $routeParameters = null)
    {
        $this->assertEquals(
            config('app.url') . $expectedPath,
            route($routeName, $routeParameters)
        );
    }
}
