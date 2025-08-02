<?php

namespace Tests\Feature;

use App\Models\User;
use Auth;
use Database\Factories\SubscriptionFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Stripe\Subscription;
use Tests\TestCase;

class MailcoachTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->markTestSkipped('Migrated from self-hosted Mailcoach to Mailcoach Cloud.');

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

    public function testUserIsAddedToAllUsersListWhenCreated()
    {
        $user = User::factory()->create();

        $this->assertNotNull(
            Subscriber::findForEmail($user->email, $this->allUsersEmailList())
        );
    }

    public function testUserIsRemovedFromAllUsersListWhenDeleted()
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertNull(
            Subscriber::findForEmail($user->email, $this->allUsersEmailList())
        );
    }

    public function testUserEmailIsUpdatedWhenChanged()
    {
        $user = User::factory()->withoutSubscription()->create(['email' => 'foo@bar.com']);

        $this->assertDatabaseHas(Subscriber::class, [
            'email' => 'foo@bar.com',
        ]);

        $user->update(['email' => 'newfoo@bar.com']);

        $this->assertDatabaseMissing(Subscriber::class, [
            'email' => 'foo@bar.com',
        ]);

        $this->assertDatabaseHas(Subscriber::class, [
            'email' => 'newfoo@bar.com',
        ]);
    }

    public function testUserIsAddedToMembersListWhenSubscriptionCreated()
    {
        $this->signIn($user = User::factory()->withoutSubscription()->create());

        $this->assertNull(
            Subscriber::findForEmail($user->email, $this->membersEmailList())
        );

        SubscriptionFactory::new()->create(['user_id' => $user->id]);

        $this->assertNotNull(
            Subscriber::findForEmail($user->email, $this->membersEmailList())
        );
    }

    public function testUserRemainsOnMembersListWhenSubscriptionUpdated()
    {
        $this->signIn();

        $this->assertNotNull(
            Subscriber::findForEmail(Auth::user()->email, $this->membersEmailList())
        );

        // Turn off auto-renew
        Auth::user()->subscription()->update([
            'ends_at' => now()->addYear(),
        ]);

        $this->assertNotNull(
            Subscriber::findForEmail(Auth::user()->email, $this->membersEmailList())
        );
    }

    public function testUserIsRemovedFromMembersListWhenSubscriptionExpires()
    {
        $this->signIn();

        $this->assertNotNull(
            Subscriber::findForEmail(Auth::user()->email, $this->membersEmailList())
        );

        Auth::user()->subscription()->update([
            'stripe_status' => Subscription::STATUS_INCOMPLETE_EXPIRED,
        ]);

        $this->assertNull(
            Subscriber::findForEmail(Auth::user()->email, $this->membersEmailList())
        );
    }

    public function testUserIsRemovedFromMembersListWhenSubscriptionIsDeleted()
    {
        $this->signIn();

        $this->assertNotNull(
            Subscriber::findForEmail(Auth::user()->email, $this->membersEmailList())
        );

        Auth::user()->subscription()->delete();

        $this->assertNull(
            Subscriber::findForEmail(Auth::user()->email, $this->membersEmailList())
        );
    }

    public function testUpdatingSubscriptionForUnsubscribedUserWorksWithoutErrors()
    {
        $this->signIn();

        Subscriber::whereEmail(Auth::user()->email)->delete();

        $this->assertNull(
            Subscriber::findForEmail(Auth::user()->email, $this->membersEmailList())
        );

        // End subscription
        Auth::user()->subscription()->update([
            'ends_at' => now(),
        ]);

        $this->assertNull(
            Subscriber::findForEmail(Auth::user()->email, $this->membersEmailList())
        );
    }

    protected function allUsersEmailList()
    {
        return EmailList::whereName('All Users')->firstOrFail();
    }

    protected function membersEmailList()
    {
        return EmailList::whereName('Members')->firstOrFail();
    }
}
