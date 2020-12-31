<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\StripeTestCase;

class SubscriptionTest extends StripeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $this->withExceptionHandling();

        $this->signIn($this->createCustomer([], true));
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testBillingPageShowsAlertIfUnsubscribed()
    {
        $this->get(route('subscription.edit'))
            ->assertSee('Ta cotisation n’est pas à jour.');
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testBillingPageShowsAlertIfActiveSubscriptionButNoPaymentMethodSaved()
    {
        $this->signIn(tap(User::factory()->create())
            ->createAsStripeCustomer(['balance' => -99999])); // Give user large credit

        Auth::user()->newSubscription('default', config('council.plans.agepac'))->add();

        $this->get(route('subscription.edit'))
            ->assertSee('Aucun moyen de paiement n’est enregistré pour ton compte.');
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testBillingPageDoesntShowAlertIfNoPaymentMethodSavedButUserCanceledSubscription()
    {
        $this->signIn(tap(User::factory()->create())
            ->createAsStripeCustomer(['balance' => -99999])); // Give user large credit

        Auth::user()->newSubscription('default', config('council.plans.agepac'))->add();

        Auth::user()->subscription('default')->cancel();

        $this->get(route('subscription.edit'))
            ->assertDontSee('role="alert"', false)
            ->assertDontSee('Aucun moyen de paiement n’est enregistré pour ton compte.');
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testBillingPageDoesntShowAlertIfSubscribed()
    {
        Auth::user()->newSubscription('default', config('council.plans.agepac'))->add();

        $this->get(route('subscription.edit'))
            ->assertDontSee('role="alert"', false);
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testAUserMustHaveADefaultPaymentMethodToSubscribe()
    {
        $this->signInUnsubscribed();

        $this->createSubscription()->assertForbidden();

        $this->assertFalse(Auth::user()->subscribed('default'));
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testPlanIsRequiredToSubscribe()
    {
        $this->createSubscription([
            'plan' => null,
        ])->assertSessionHasErrors('plan');

        $this->assertFalse(Auth::user()->subscribed('default'));
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testPlanMustExistInConfig()
    {
        $this->createSubscription([
            'plan' => 'foobar',
        ])->assertSessionHasErrors('plan');

        $this->assertFalse(Auth::user()->subscribed('default'));
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanSubscribeToAPlan()
    {
        $this->createSubscription([
            'plan' => Arr::random(array_keys(config('council.plans'))),
        ])
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('subscription.edit'));

        $this->assertTrue(Auth::user()->subscribed('default'));
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanChangeToAnotherPlan()
    {
        ($user = Auth::user())->newSubscription('default', config('council.plans.agepac'))->add();

        $this->assertTrue($user->subscribedToPlan(config('council.plans.agepac'), 'default'));

        $this->updateSubscription(['plan' => 'agepac+alumni'])->assertOk();

        $this->assertFalse($user->subscribedToPlan(config('council.plans.agepac'), 'default'));
        $this->assertTrue($user->subscribedToPlan(config('council.plans.agepac+alumni'), 'default'));
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testChangingToSamePlanDoesntCauseError()
    {
        ($user = Auth::user())->newSubscription('default', config('council.plans.agepac'))->add();

        $this->assertTrue($user->subscribedToPlan(config('council.plans.agepac'), 'default'));

        $this->updateSubscription(['plan' => 'agepac'])->assertOk();
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanCancelTheirSubscription()
    {
        ($user = Auth::user())->newSubscription('default', config('council.plans.agepac'))->add();

        $this->assertTrue($user->subscribed('default'));
        $this->assertFalse($user->subscription('default')->onGracePeriod());

        $this->updateSubscription(['active' => false])->assertOk();

        $this->assertTrue($user->subscribed('default'));
        $this->assertTrue($user->subscription('default')->onGracePeriod());
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanResumeTheirCancelledSubscription()
    {
        ($user = Auth::user())->newSubscription('default', config('council.plans.agepac'))->add()->cancel();

        $this->assertTrue($user->subscribed('default'));
        $this->assertTrue($user->subscription('default')->onGracePeriod());

        $this->updateSubscription(['active' => true])->assertOk();

        $this->assertTrue($user->subscribed('default'));
        $this->assertFalse($user->subscription('default')->onGracePeriod());
    }

    /**
     * Send a post request to create a subscription.
     *
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    public function createSubscription(array $data = [])
    {
        return $this->post(route('subscription.store'), $data);
    }

    /**
     * Send a patch request to update a subscription.
     *
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    public function updateSubscription(array $data = [])
    {
        return $this->patchJson(route('subscription.update'), $data);
    }
}
