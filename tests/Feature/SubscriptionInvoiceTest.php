<?php

namespace Tests\Feature;

use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Auth;
use Tests\StripeTestCase;

class SubscriptionInvoiceTest extends StripeTestCase
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
    public function testUserWithoutSubscriptionCannotSeeListOfInvoices()
    {
        $this->get(route('subscription.edit'))
            ->assertDontSee('Factures');
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserWithSubscriptionCanSeeAtLeastOneInvoice()
    {
        $this->subscribeUser();

        $this->get(route('subscription.edit'))
            ->assertSee('Factures')
            ->assertSee(today()->format('j F Y'));
    }

    /**
     * @test
     * @group external-api
     * @group stripe-api
     */
    public function testUserCanDownloadInvoice()
    {
        SnappyPdf::fake();

        $this->subscribeUser();

        $this->get(route('subscription.invoices.show', Auth::user()->invoices()->first()->id))
            ->assertOk();

        SnappyPdf::assertSee('AGEPAC');
        SnappyPdf::assertSee('Facture');
    }

    /**
     * Associate the user with a subscription.
     *
     * @param  \App\Models\User|null  $user
     * @return void
     */
    protected function subscribeUser(User $user = null): void
    {
        ($user ?? Auth::user())->newSubscription(
            'default',
            config('council.plans.agepac')
        )->add();
    }
}
