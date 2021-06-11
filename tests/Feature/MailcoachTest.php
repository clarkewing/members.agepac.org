<?php

namespace Tests\Feature;

use Tests\TestCase;

class MailcoachTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    public function testRedirectsToCampaignsByDefault()
    {
        $this->get('/mailcoach')
            ->assertRedirect('/mailcoach/campaigns');
    }

    public function testGuestsCannotSeeMailcoachDashboard()
    {
        $this->get('/mailcoach/campaigns')
            ->assertRedirect(route('login'));
    }

    public function testUnauthorizedUsersCannotSeeMailcoachDashboard()
    {
        $this->signIn();

        $this->get('/mailcoach/campaigns')
            ->assertRedirect(route('login'));
    }

    public function testAuthorizedUsersCanSeeMailcoachDashboard()
    {
        $this->signInWithPermission('viewMailcoachDashboard');

        $this->get('/mailcoach/campaigns')
            ->assertSuccessful();
    }

    public function testAdminsCanSeeMailcoachDashboard()
    {
        $this->signInWithRole('Administrator');

        $this->get('/mailcoach/campaigns')
            ->assertSuccessful();
    }
}
