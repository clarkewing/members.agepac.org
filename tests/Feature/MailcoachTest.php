<?php

namespace Tests\Feature;

use App\Models\User;
use Auth;
use Database\Factories\SubscriptionFactory;
use Stripe\Subscription;
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

    public function testUserIsAddedToMembersListWhenSubscriptionCreated()
    {
        $this->signIn($user = User::factory()->withoutSubscription()->create());

        $this->assertDatabaseMissing('mailcoach_subscribers', [
            'email' => Auth::user()->email
        ]);

        SubscriptionFactory::new()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('mailcoach_subscribers', [
            'email' => Auth::user()->email
        ]);
    }

    public function testUserRemainsOnMembersListWhenSubscriptionUpdated()
    {
        $this->signIn();

        $this->assertDatabaseHas('mailcoach_subscribers', [
            'email' => Auth::user()->email
        ]);

        // Turn off auto-renew
        Auth::user()->subscription()->update([
            'ends_at' => now()->addYear(),
        ]);

        $this->assertDatabaseHas('mailcoach_subscribers', [
            'email' => Auth::user()->email
        ]);
    }

    public function testUserIsRemovedFromMembersListWhenSubscriptionExpires()
    {
        $this->signIn();

        $this->assertDatabaseHas('mailcoach_subscribers', [
            'email' => Auth::user()->email
        ]);

        Auth::user()->subscription()->update([
            'stripe_status' => Subscription::STATUS_INCOMPLETE_EXPIRED,
        ]);

        $this->assertDatabaseMissing('mailcoach_subscribers', [
            'email' => Auth::user()->email
        ]);
    }

    public function testUserIsRemovedFromMembersListWhenSubscriptionIsDeleted()
    {
        $this->signIn();

        $this->assertDatabaseHas('mailcoach_subscribers', [
            'email' => Auth::user()->email
        ]);

        Auth::user()->subscription()->delete();

        $this->assertDatabaseMissing('mailcoach_subscribers', [
            'email' => Auth::user()->email
        ]);
    }
}
