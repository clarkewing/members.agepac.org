<?php

namespace Tests\Feature;

use App\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Auth;
use Tests\StripeTestCase;

class SubscriptionInvoiceTest extends StripeTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->signIn($this->createCustomer([], true));
    }

    /** @test */
    public function testUserWithoutSubscriptionCannotSeeListOfInvoices()
    {
        $this->get(route('subscription.edit'))
            ->assertDontSee('Factures');
    }

    /** @test */
    public function testUserWithSubscriptionCanSeeAtLeastOneInvoice()
    {
        $this->subscribeUser();

        $this->get(route('subscription.edit'))
            ->assertSee('Factures')
            ->assertSee(today()->format('j F Y'));
    }

    /** @test */
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
     * @param  \App\User|null  $user
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