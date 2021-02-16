<?php

namespace Tests\Feature\Nova;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ImpersonateUsersTest extends TestCase
{
    protected $impersonationTarget;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->impersonationTarget = User::factory()->create();
    }

    /** @test */
    public function testGuestsCannotImpersonate()
    {
        $this->impersonate($this->impersonationTarget)
            ->assertRedirect(route('nova.login'));

        $this->assertNull(Auth::user());
        $this->assertFalse(app('impersonate')->isImpersonating());
    }

    /** @test */
    public function testUnauthorizedUsersCannotImpersonate()
    {
        $this->signIn();

        $this->impersonate($this->impersonationTarget)
            ->assertForbidden();

        $this->assertFalse(Auth::user()->is($this->impersonationTarget));
        $this->assertFalse(app('impersonate')->isImpersonating());
    }

    /** @test */
    public function testAuthorizedUsersCanImpersonate()
    {
        $this->signInWithPermission('impersonate');

        $this->impersonate($this->impersonationTarget)
            ->assertRedirect('/');

        $this->assertTrue(Auth::user()->is($this->impersonationTarget));
        $this->assertTrue(app('impersonate')->isImpersonating());
    }

    /** @test */
    public function testCanStopImpersonating()
    {
        $this->signInWithPermission('impersonate');
        $impersonator = Auth::user();

        $this->impersonate($this->impersonationTarget);

        $this->assertTrue(app('impersonate')->isImpersonating());

        $this->stopImpersonating()
            ->assertRedirect();

        $this->assertTrue(Auth::user()->is($impersonator));
        $this->assertFalse(app('impersonate')->isImpersonating());
    }

    protected function impersonate(User $user = null): \Illuminate\Testing\TestResponse
    {
        return $this->get(route('nova.impersonate.take', ($user ?? User::factory()->create())->id));
    }

    protected function stopImpersonating(): \Illuminate\Testing\TestResponse
    {
        return $this->get(route('nova.impersonate.leave'));
    }
}
