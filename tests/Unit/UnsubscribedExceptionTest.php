<?php

namespace Tests\Unit;

use App\Exceptions\UnsubscribedException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UnsubscribedExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        Route::any('exception-test', function () {
            throw new UnsubscribedException;
        });
    }

    /** @test */
    public function testExceptionHandlerRedirectsToBillingPage()
    {
        $this->get('exception-test')
            ->assertRedirect(route('subscription.edit'));
    }

    /** @test */
    public function testExceptionHandlerReturns402StatusForJsonRequest()
    {
        $this->getJson('exception-test')
            ->assertPaymentRequired();
    }
}
