<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\NewUnverifiedUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ManageUserVerificationTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testAppropriateUsersAreNotifiedOfNewUnverifiedUser()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo('users.verify');
        $userWithoutPermission = User::factory()->create();

        Notification::fake();

        $this->user = User::factory()->unverified()->create();
        event(new Registered($this->user));

        Notification::assertSentTo($userWithPermission, NewUnverifiedUser::class);
        Notification::assertNotSentTo($userWithoutPermission, NewUnverifiedUser::class);
    }
}
