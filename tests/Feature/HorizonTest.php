<?php

namespace Tests\Feature;

use Tests\TestCase;

class HorizonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    public function testGuestsCannotSeeHorizonDashboard()
    {
        $this->get('/horizon')
            ->assertForbidden();
    }

    public function testUnauthorizedUsersCannotSeeHorizonDashboard()
    {
        $this->signIn();

        $this->get('/horizon')
            ->assertForbidden();
    }

    public function testAuthorizedUsersCanSeeHorizonDashboard()
    {
        $this->signInWithPermission('viewHorizonDashboard');

        $this->get('/horizon')
            ->assertSuccessful();
    }

    public function testAdminsCanSeeHorizonDashboard()
    {
        $this->signInWithRole('Administrator');

        $this->get('/horizon')
            ->assertSuccessful();
    }
}
