<?php

namespace Tests\Unit;

use App\Models\Thread;
use App\Trending;
use Tests\TestCase;

class TrendingTest extends TestCase
{
    /**
     * @var \App\Trending
     */
    protected $trending;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trending = new Trending;

        $this->trending->reset();
    }

    /** @test */
    public function IncrementsScoreEachTimeAThreadIsPushed()
    {
        $thread = Thread::factory()->create();

        $this->assertEquals(0, $this->trending->score($thread));

        $this->trending->push($thread);

        $this->assertEquals(1, $this->trending->score($thread));
    }

    /** @test */
    public function ReturnsTheTop5Threads()
    {
        $threads = [
            1 => Thread::factory()->create(),
            2 => Thread::factory()->create(),
            3 => Thread::factory()->create(),
            4 => Thread::factory()->create(),
            5 => Thread::factory()->create(),
            6 => Thread::factory()->create(),
        ];

        $this->trending->push($threads[1], 1);
        $this->trending->push($threads[2], 2);
        $this->trending->push($threads[3], 3);
        $this->trending->push($threads[4], 4);
        $this->trending->push($threads[5], 5);
        $this->trending->push($threads[6], 6);

        tap($this->trending->get(), function ($trending) use ($threads) {
            $this->assertCount(5, $trending);
            $this->assertEquals($threads[6]->path(), $trending[0]->path);
            $this->assertEquals($threads[5]->path(), $trending[1]->path);
            $this->assertEquals($threads[4]->path(), $trending[2]->path);
            $this->assertEquals($threads[3]->path(), $trending[3]->path);
            $this->assertEquals($threads[2]->path(), $trending[4]->path);
        });
    }

    /** @test */
    public function ReturnsAnEmptyCollectionWhenThereAreNoTrendingThreads()
    {
        $this->assertCount(0, $this->trending->get());
    }
}
