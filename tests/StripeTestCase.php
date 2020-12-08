<?php

namespace Tests;

use App\User;

abstract class StripeTestCase extends TestCase
{
    protected function tearDown(): void
    {
        // Ensure created Stripe customers aren't persisted.
        User::whereNotNull('stripe_id')->get()->map(function ($user) {
            $user->asStripeCustomer()->delete();
        });

        parent::tearDown();
    }

    /**
     * Create a Stripe customer with a payment method.
     *
     * @param  array  $overrides
     * @param  bool  $withDefaultPaymentMethod
     * @param  string  $paymentMethodId
     * @return mixed
     */
    public function createCustomer(
        array $overrides = [],
        bool $withDefaultPaymentMethod = false,
        string $paymentMethodId = 'pm_card_visa'
    ) {
        $user = tap(User::factory()->create($overrides))->createAsStripeCustomer();

        $user->{$withDefaultPaymentMethod ? 'updateDefaultPaymentMethod' : 'addPaymentMethod'}($paymentMethodId);

        return $user;
    }
}
