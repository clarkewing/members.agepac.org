<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\UserPendingApproval;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ManageUserApprovalTest extends TestCase
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
}
