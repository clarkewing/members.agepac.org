<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /** @test */
    public function testAUserCanSearchThreads()
    {
        if (! config('scout.algolia.id')) {
            $this->markTestSkipped('Algolia is not configured.');
        }

        config(['scout.driver' => 'algolia']);

        $search = 'foobar';

        create(Thread::class, [], 2);
        create(Thread::class, ['body' => "A body with the {$search} term."], 2);

        do {
            sleep(.25);

        } while (empty($results));
            $results = $this->getJson(route('threads.search') . "?query=$search")->json()['data'];

        $this->assertCount(2, $results);

        Thread::latest()->take(4)->unsearchable();
    }
}
