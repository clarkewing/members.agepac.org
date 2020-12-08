<?php

namespace Tests\Feature;

use App\Post;
use App\Thread;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * @test
     * @group external-api
     * @group algolia-api
     */
    public function testAUserCanSearchTheForum()
    {
        if (! config('scout.algolia.id')) {
            $this->markTestSkipped('Algolia is not configured.');
        }

        config(['scout.driver' => 'algolia']);

        $search = 'foobar';

        Thread::factory()->count(2)->create();
        Thread::factory()->create(['title' => "A title with the {$search} term"]);
        Thread::factory()->create(['title' => "Another title with the {$search} term"]);

        $maxTime = now()->addSeconds(20);

        do {
            sleep(.25);

            $results = $this->getJson(route('threads.search') . "?query=$search")->json()['data'];
        } while (empty($results) && now()->lessThan($maxTime));

        $this->assertCount(2, $results);

        // Clean up index.
        Post::latest()->take(4)->unsearchable();
    }
}
