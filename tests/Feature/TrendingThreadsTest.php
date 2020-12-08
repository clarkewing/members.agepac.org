<?php

namespace Tests\Feature;

use App\Thread;
use App\Trending;
use Tests\TestCase;

class TrendingThreadsTest extends TestCase
{
    /**
     * @var \App\Trending
     */
    protected $trending;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();

        $this->trending = new Trending;

        $this->trending->reset();
    }

    /** @test */
    public function testIncrementsThreadScoreEveryTimeItIsRead()
    {
        $this->assertCount(0, $this->trending->get());

        $thread = Thread::factory()->create();
        $this->get($thread->path());

        $this->assertCount(1, $trending = $this->trending->get());
        $this->assertEquals($thread->title, $trending[0]->title);
    }
}
