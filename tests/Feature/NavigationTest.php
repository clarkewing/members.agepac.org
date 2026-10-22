<?php

namespace Tests\Feature;

use Tests\TestCase;

class NavigationTest extends TestCase
{
    /** @test */
    public function testGuestsAreSentToTheNewAppToLogIn()
    {
        $this->get('/')
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertRedirect(config('handoff.target_host') . '/login');
    }

    /** @test */
    public function testGuestsCannotSeeLinksForAuthenticatedMembers()
    {
        $this->get(route('pending-approval'))
            ->assertSuccessful()
            ->assertDontSee('<!-- Authenticated Member links -->', false);
    }

    /** @test */
    public function testUsersCanSeeLinksForAuthenticatedMembers()
    {
        $this->signIn();

        $this->followingRedirects()->get('/')
            ->assertSuccessful()
            ->assertSee('<!-- Authenticated Member links -->', false);
    }
}
