<?php

namespace Tests\Feature;

use App\Events\UserApproved;
use App\Models\User;
use App\Notifications\UserPendingApproval;
use App\Notifications\YourAccountIsApproved;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserApprovalTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testAppropriateUsersAreNotifiedOfNewUnapprovedUser()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo('users.approve');
        $userWithoutPermission = User::factory()->create();

        Notification::fake();

        $this->user = User::factory()->unapproved()->create();
        event(new Registered($this->user));

        Notification::assertSentTo($userWithPermission, UserPendingApproval::class);
        Notification::assertNotSentTo($userWithoutPermission, UserPendingApproval::class);
    }

    /** @test */
    public function testAnUnapprovedUserIsRedirectedUponLogin()
    {
        $user = User::factory()->unapproved()->create();

        $this->assertFalse($user->isApproved());

        $this->actingAs($user)
            ->get(route('home'))
            ->assertRedirect(route('pending-approval'));
    }

    /** @test */
    public function testAnApprovedUserIsNotRedirectedUponLogin()
    {
        $user = User::factory()->create();

        $this->assertTrue($user->isApproved());

        $this->actingAs($user)
            ->get(route('home'))
            ->assertNotRedirect(route('pending-approval'))
            ->assertSuccessful();
    }

    /** @test */
    public function testAnUnapprovedUserCanAccessPendingApprovalRoute()
    {
        $user = User::factory()->unapproved()->create();

        $this->assertFalse($user->isApproved());

        $this->be($user);

        $this->post(route('pending-approval'))
            ->assertNotRedirect(route('pending-approval'));
    }

    /** @test */
    public function testUserIsNotifiedWhenTheirAccountIsApproved()
    {
        $user = User::factory()->unapproved()->create();

        Notification::fake();

        event(new UserApproved($user));

        Notification::assertSentTo($user, YourAccountIsApproved::class);
    }
}
