<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class HomeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        foreach (range(1, 8) as $i) {
            $this->travel(-$i)->days();

            Thread::factory()->create();
        }

        $this->travelBack();
    }

    /** @test */
    public function testGuestsCannotSeeHome()
    {
        Auth::logout();

        $this->get(route('home'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUsersCanSeeHome()
    {
        $this->get(route('home'))
            ->assertOk();
    }

    /** @test */
    public function testThreadUpdatesContains8LatestThreadsPosted()
    {
        $viewThreadUpdates = $this->get(route('home'))
            ->viewData('threadUpdates');

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $viewThreadUpdates->pluck('id')->all());
    }

    /** @test */
    public function testThreadUpdatesHasThreadWithLatestReplyFirst()
    {
        Post::factory()->create(['thread_id' => 3]);

        $viewThreadUpdates = $this->get(route('home'))
            ->viewData('threadUpdates');

        $this->assertEquals([3, 1, 2, 4, 5, 6, 7, 8], $viewThreadUpdates->pluck('id')->all());
    }

    /** @test */
    public function testUpdatingAThreadDoesNotAffectOrder()
    {
        Thread::find(5)->update(['title' => 'New title']);

        $viewThreadUpdates = $this->get(route('home'))
            ->viewData('threadUpdates');

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $viewThreadUpdates->pluck('id')->all());
    }

    /** @test */
    public function testThreadUpdatesShowsOnlyThreadsUserCanSee()
    {
        Thread::find(1)->channel->createPermission('view');

        $viewThreadUpdates = $this->get(route('home'))
            ->viewData('threadUpdates');

        $this->assertNotContains(1, $viewThreadUpdates->pluck('id')->all());
    }
}
