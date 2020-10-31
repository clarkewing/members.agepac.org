<?php

namespace Tests\Feature;

use Tests\TestCase;

class NavigationTest extends TestCase
{
    /** @test */
    public function testGuestsCannotSeeLinksForAuthenticatedMembers()
    {
        $this->followingRedirects()->get('/')
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
