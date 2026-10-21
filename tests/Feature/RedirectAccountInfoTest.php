<?php

namespace Tests\Feature;

use Tests\TestCase;

class RedirectAccountInfoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        config(['handoff.target_host' => 'http://squawk.agepac.org.test']);
    }

    /** @test */
    public function testMembersAreHandedOffToTheirProfileOnTheMembersApp()
    {
        $this->signIn();

        $location = $this->get(route('account.edit'))
            ->assertStatus(302)
            ->headers->get('Location');

        $this->assertStringStartsWith('http://squawk.agepac.org.test/handoff?', $location);
        $this->assertStringContainsString('target=' . urlencode('/settings/profile'), $location);
        $this->assertStringContainsString('signature=', $location);
    }

    /** @test */
    public function testGuestsMustLogInBeforeBeingHandedOff()
    {
        $this->get(route('account.edit'))
            ->assertRedirect(route('login'));
    }
}
