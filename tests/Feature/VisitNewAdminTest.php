<?php

namespace Tests\Feature;

use Tests\TestCase;

class VisitNewAdminTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        config(['handoff.target_host' => 'http://squawk.agepac.org.test']);
    }

    /** @test */
    public function testAdminsAreHandedOffToTheNewAdminPanel()
    {
        $this->signInWithPermission('pages.viewUnpublished');

        $location = $this->get(route('new-admin'))
            ->assertStatus(302)
            ->headers->get('Location');

        $this->assertStringStartsWith('http://squawk.agepac.org.test/handoff?', $location);
        $this->assertStringContainsString('target='.urlencode('/admin'), $location);
        $this->assertStringContainsString('signature=', $location);
    }

    /** @test */
    public function testRegularUsersCannotReachTheHandoff()
    {
        $this->signIn();

        $this->get(route('new-admin'))
            ->assertForbidden();
    }

    /** @test */
    public function testGuestsAreRedirectedToLogin()
    {
        $this->get(route('new-admin'))
            ->assertRedirect(route('login'));
    }
}
