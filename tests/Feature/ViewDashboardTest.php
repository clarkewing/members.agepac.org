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
    public function testUnsubscribedUsersCannotSeeDashboard()
    {
        $this->signInUnsubscribed();

        $this->getDashboard()
            ->assertRedirect(route('subscription.edit'));
    }

    /** @test */
    public function testSubscribedUsersCanSeeDashboard()
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
