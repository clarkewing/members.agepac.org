<?php

namespace Tests\Feature;

use Tests\TestCase;

class RedirectLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testGuestsAreSentToTheNewAppToLogIn()
    {
        $this->get(route('login'))
            ->assertRedirect(config('handoff.target_host').'/login');
    }

    /** @test */
    public function testGuestsLogInOnTheNewAppWithThePageTheyWanted()
    {
        $this->get('/home')
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertRedirect(config('handoff.target_host').'/login?intended='.urlencode('/home'));
    }

    /** @test */
    public function testGuestsCannotLogInHere()
    {
        $this->post('/login', ['email' => 'member@example.com', 'password' => 'password'])
            ->assertRedirect(config('handoff.target_host').'/login');

        $this->assertGuest();
    }

    /** @test */
    public function testMembersAlreadyLoggedInHereAreNotSentAway()
    {
        $this->signIn();

        $this->get(route('login'))
            ->assertRedirect('/home');
    }

    /** @test */
    public function testGuestsResetTheirPasswordOnTheNewApp()
    {
        $this->get(route('password.request'))
            ->assertRedirect(config('handoff.target_host').'/forgot-password');
    }

    /** @test */
    public function testAdminsLogInToNovaOnTheNewApp()
    {
        $this->get('/nova')
            ->assertRedirect('/nova/login');

        $this->get('/nova/login')
            ->assertRedirect(config('handoff.target_host').'/login?intended='.urlencode('/nova'));
    }
}
