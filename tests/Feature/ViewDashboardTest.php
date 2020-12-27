<?php

namespace Tests\Feature;

use Tests\TestCase;

class ViewDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testGuestsCannotSeeDashboard()
    {
        $this->getDashboard()
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUsersCanSeeDashboard()
    {
        $this->signIn();

        $this->getDashboard()
            ->assertSuccessful()
            ->assertViewIs('home');
    }

    protected function getDashboard()
    {
        return $this->get(route('home'));
    }
}
