<?php

namespace Tests\Feature\Nova;

use Tests\TestCase;

class NovaTest extends TestCase
{
    public function setUp(): void
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
    public function testUsersWithPermissionsCanAccessNovaInterface()
    {
        $this->signInWithPermission('users.view');

        $this->getNovaDashboard()
            ->assertSuccessful();
    }

    /**
     * @return \Illuminate\Testing\TestResponse
     */
    protected function getNovaDashboard(): \Illuminate\Testing\TestResponse
    {
        return $this->get(config('nova.path'));
    }
}
