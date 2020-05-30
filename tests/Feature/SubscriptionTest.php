<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\StripeTestCase;

class SubscriptionTest extends StripeTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->signIn($this->createCustomer([], true));
    }

    /** @test */
    public function testAUserMustHaveADefaultPaymentMethodToSubscribe()
    {
        $this->signIn();

        $this->createSubscription()->assertForbidden();

        $this->assertFalse(Auth::user()->subscribed('default'));
    }

    /** @test */
    public function testPlanIsRequiredToSubscribe()
    {
        $this->createSubscription([
            'plan' => null,
        ])->assertSessionHasErrors('plan');

        $this->assertFalse(Auth::user()->subscribed('default'));
    }

    /** @test */
    public function testPlanMustExistInConfig()
    {
        $this->createSubscription([
            'plan' => 'foobar',
        ])->assertSessionHasErrors('plan');

        $this->assertFalse(Auth::user()->subscribed('default'));
    }

    /** @test */
    public function testUserCanSubscribeToAPlan()
    {
        $this->createSubscription([
            'plan' => Arr::random(array_keys(config('council.plans'))),
        ])
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('subscription.edit'));

        $this->assertTrue(Auth::user()->subscribed('default'));
    }

    /** @test */
    public function testUserCanChangeToAnotherPlan()
    {
        ($user = Auth::user())->newSubscription('default', config('council.plans.agepac'))->add();

        $this->assertTrue($user->subscribedToPlan(config('council.plans.agepac'), 'default'));

        $this->updateSubscription(['plan' => 'agepac+alumni'])->assertOk();

        $this->assertFalse($user->subscribedToPlan(config('council.plans.agepac'), 'default'));
        $this->assertTrue($user->subscribedToPlan(config('council.plans.agepac+alumni'), 'default'));
    }

    /** @test */
    public function testChangingToSamePlanDoesntCauseError()
    {
        ($user = Auth::user())->newSubscription('default', config('council.plans.agepac'))->add();

        $this->assertTrue($user->subscribedToPlan(config('council.plans.agepac'), 'default'));

        $this->updateSubscription(['plan' => 'agepac'])->assertOk();
    }

    /** @test */
    public function testUserCanCancelTheirSubscription()
    {
        ($user = Auth::user())->newSubscription('default', config('council.plans.agepac'))->add();

        $this->assertTrue($user->subscribed('default'));
        $this->assertFalse($user->subscription('default')->onGracePeriod());

        $this->updateSubscription(['active' => false])->assertOk();

        $this->assertTrue($user->subscribed('default'));
        $this->assertTrue($user->subscription('default')->onGracePeriod());
    }

    /** @test */
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
