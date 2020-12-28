<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tests\StripeTestCase;

class PaymentMethodsTest extends StripeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signInUnsubscribed();
    }

    /** @test */
    public function testGuestCannotGetPaymentIntent()
    {
        Auth::logout();

        $this->get(route('subscription.payment-methods.create'))
            ->assertRedirect(route('login'));
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanGetPaymentIntent()
    {
        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $this->get(route('subscription.payment-methods.create'))
            ->assertOk()
            ->assertJsonStructure(['intent' => ['client_secret']]);
    }

    /** @test */
    public function testGuestCannotAddPaymentMethod()
    {
        Auth::logout();

        $this->addPaymentMethod()->assertUnauthorized();
    }

    /** @test */
    public function testPaymentMethodRequired()
    {
        $this->addPaymentMethod(['payment_method' => null])
            ->assertJsonValidationErrors('payment_method');
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanAddPaymentMethod()
    {
        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $this->addPaymentMethod()->assertCreated();

        $this->assertCount(1, Auth::user()->paymentMethods());
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testFirstPaymentMethodSetAsDefaultAndUnaffectedBySubsequent()
    {
        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $firstPaymentMethod = $this->addPaymentMethod()->json('id');

        $this->assertCount(1, Auth::user()->paymentMethods());
        $this->assertEquals(
            $firstPaymentMethod,
            Auth::user()->defaultPaymentMethod()->id
        );

        $secondPaymentMethod = $this->addPaymentMethod()->json('id');

        $this->assertCount(2, Auth::user()->paymentMethods());
        $this->assertNotEquals(
            $secondPaymentMethod,
            Auth::user()->fresh()->defaultPaymentMethod()->id
        );
    }

    /** @test */
    public function testGuestCannotDeletePaymentMethod()
    {
        Auth::logout();

        $this->deleteJson(route('subscription.payment-methods.destroy', 'pm_foobar'))
            ->assertUnauthorized();
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testOnlyPaymentMethodOwnerCanDeleteIt()
    {
        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $customer = $this->createCustomer();

        $this->assertCount(1, $customer->paymentMethods());

        // Other user attempts to delete.
        $this->deleteJson(route(
            'subscription.payment-methods.destroy',
            $customer->paymentMethods()->first()->id
        ))
            ->assertForbidden();

        $this->assertCount(1, $customer->paymentMethods());
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanDeleteDefaultPaymentMethod()
    {
        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $this->signIn($customer = $this->createCustomer([], true));

        $this->assertNotNull($customer->defaultPaymentMethod());
        $this->assertCount(1, $customer->paymentMethods());

        $this->deleteJson(route(
            'subscription.payment-methods.destroy',
            $customer->paymentMethods()->first()->id
        ))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertCount(1, $customer->paymentMethods());
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanDeletePaymentMethod()
    {
        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $this->signIn($customer = $this->createCustomer());

        $this->assertCount(1, $customer->paymentMethods());

        $this->deleteJson(route(
            'subscription.payment-methods.destroy',
            $customer->paymentMethods()->first()->id
        ))
            ->assertNoContent();

        $this->assertCount(0, $customer->paymentMethods());
    }

    /** @test */
    public function testGuestCannotUpdatePaymentMethod()
    {
        Auth::logout();

        $this->patchJson(route('subscription.payment-methods.update', 'pm_foobar'))
            ->assertUnauthorized();
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testOnlyPaymentMethodOwnerCanUpdateIt()
    {
        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $customer = $this->createCustomer();

        // Other user attempts to delete.
        $this->putJson(route(
            'subscription.payment-methods.update',
            $customer->paymentMethods()->first()->id
        ))
            ->assertForbidden();
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanSetPaymentMethodAsDefault()
    {
        if (! config('cashier.key')) {
            $this->markTestSkipped('Cashier is not configured.');
        }

        $this->signIn($customer = $this->createCustomer());
        $paymentMethod = $customer->paymentMethods()->first();

        $this->assertNull($customer->defaultPaymentMethod());

        $this->putJson(route('subscription.payment-methods.update', $paymentMethod->id), [
            'default' => true,
        ])
            ->assertNoContent();

        $this->assertEquals($paymentMethod->id, $customer->defaultPaymentMethod()->id);
    }

    /**
     * Send a post request to add a payment method.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function addPaymentMethod(array $overrides = [])
    {
        return $this->postJson(route('subscription.payment-methods.store'), array_merge([
            'payment_method' => 'pm_card_visa',
        ], $overrides));
    }
}
