<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PaymentMethodsTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    public function tearDown(): void
    {
        // Ensure created Stripe customers aren't persisted.
        User::whereNotNull('stripe_id')->get()->map(function ($user) {
            $user->asStripeCustomer()->delete();
        });

        parent::tearDown();
    }

    /** @test */
    public function testGuestCannotGetPaymentIntent()
    {
        $this->get(route('account.billing.payment-methods.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testUserCanGetPaymentIntent()
    {
        $this->signIn();

        $this->get(route('account.billing.payment-methods.create'))
            ->assertOk()
            ->assertJsonStructure(['intent' => ['client_secret']]);
    }

    /** @test */
    public function testGuestCannotAddPaymentMethod()
    {
        $this->addPaymentMethod()->assertUnauthorized();
    }

    /** @test */
    public function testPaymentMethodRequired()
    {
        $this->signIn();

        $this->addPaymentMethod(['payment_method' => null])
            ->assertJsonValidationErrors('payment_method');
    }

    /** @test */
    public function testUserCanAddPaymentMethod()
    {
        $this->signIn();

        $this->addPaymentMethod()->assertCreated();

        $this->assertCount(1, Auth::user()->paymentMethods());
    }

    /** @test */
    public function testFirstPaymentMethodSetAsDefaultAndUnaffectedBySubsequent()
    {
        $this->signIn();

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
        $this->deleteJson(route('account.billing.payment-methods.destroy', 'pm_foobar'))
            ->assertUnauthorized();
    }

    /** @test */
    public function testOnlyPaymentMethodOwnerCanDeleteIt()
    {
        $customer = tap(create(User::class))->createAsStripeCustomer();
        $paymentMethod = $customer->addPaymentMethod('pm_card_visa');

        $this->assertCount(1, $customer->paymentMethods());

        // Other user attempts to delete.
        $this->signIn()
            ->deleteJson(route('account.billing.payment-methods.destroy', $paymentMethod->id))
            ->assertForbidden();

        $this->assertCount(1, $customer->paymentMethods());
    }

    /** @test */
    public function testUserCanDeleteDefaultPaymentMethod()
    {
        $customer = tap(create(User::class))->createAsStripeCustomer();
        $paymentMethod = $customer->updateDefaultPaymentMethod('pm_card_visa');

        $this->assertNotNull($customer->defaultPaymentMethod());
        $this->assertCount(1, $customer->paymentMethods());

        $this->signIn($customer)
            ->deleteJson(route('account.billing.payment-methods.destroy', $paymentMethod->id))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertCount(1, $customer->paymentMethods());
    }

    /** @test */
    public function testUserCanDeletePaymentMethod()
    {
        $customer = tap(create(User::class))->createAsStripeCustomer();
        $paymentMethod = $customer->addPaymentMethod('pm_card_visa');

        $this->assertCount(1, $customer->paymentMethods());

        $this->signIn($customer)
            ->deleteJson(route('account.billing.payment-methods.destroy', $paymentMethod->id))
            ->assertNoContent();

        $this->assertCount(0, $customer->paymentMethods());
    }

    /** @test */
    public function testGuestCannotUpdatePaymentMethod()
    {
        $this->patchJson(route('account.billing.payment-methods.update', 'pm_foobar'))
            ->assertUnauthorized();
    }

    /** @test */
    public function testOnlyPaymentMethodOwnerCanUpdateIt()
    {
        $customer = tap(create(User::class))->createAsStripeCustomer();
        $paymentMethod = $customer->addPaymentMethod('pm_card_visa');

        // Other user attempts to delete.
        $this->signIn()
            ->putJson(route('account.billing.payment-methods.update', $paymentMethod->id))
            ->assertForbidden();
    }

    /** @test */
    public function testUserCanSetPaymentMethodAsDefault()
    {
        $customer = tap(create(User::class))->createAsStripeCustomer();
        $paymentMethod = $customer->addPaymentMethod('pm_card_visa');

        $this->assertNull($customer->defaultPaymentMethod());

        $this->signIn($customer)
            ->putJson(route('account.billing.payment-methods.update', $paymentMethod->id), [
                'default' => true,
            ])->assertNoContent();

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
        return $this->postJson(route('account.billing.payment-methods.store'), array_merge([
            'payment_method' => 'pm_card_visa',
        ], $overrides));
    }
}
