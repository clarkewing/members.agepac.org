<?php

namespace Tests\Feature\Nova;

use App\User;
use Tests\TestCase;

class NovaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testUnauthenticatedUsersAreRedirectedToLogin()
    {
        $this->getNovaDashboard()
            ->assertRedirect(route('nova.login'));
    }

    /** @test */
    public function testUnauthorizedUsersCannotAccessNovaInterface()
    {
        $this->signIn();

        $this->getNovaDashboard()
            ->assertForbidden();
    }

    /** @test */
    public function testUsersWithAPermissionCanAccessNovaInterface()
    {
        $this->signInWithPermission('roles&permissions.manage');

        $this->getNovaDashboard()
            ->assertSuccessful();
    }

    /** @test */
    public function testUsersWithARoleCanAccessNovaInterface()
    {
        $this->signIn(create(User::class)->assignRole('Administrator'));

        $this->getNovaDashboard()
            ->assertSuccessful();
    }

    /** @test */
    public function testUserWithoutNovaAccessCannotSeeAdministrationLinkInNavbar()
    {
        $this->signIn();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('href="/nova"', false);
    }

    /** @test */
    public function testUserWithAPermissionCanSeeAdministrationLinkInNavbar()
    {
        $this->signInWithPermission('roles&permissions.manage');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="/nova"', false);
    }

    /** @test */
    public function testUserWithARoleCanSeeAdministrationLinkInNavbar()
    {
        $this->signIn(create(User::class)->assignRole('Administrator'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="/nova"', false);
    }

    /**
     * @return \Illuminate\Testing\TestResponse
     */
    protected function getNovaDashboard(): \Illuminate\Testing\TestResponse
    {
        return $this->get(config('nova.path'));
    }
}
