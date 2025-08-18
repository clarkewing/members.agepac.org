<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->hasDefaultPaymentMethod(), 403);

        $request->validate([
            'plan' => [
                'required',
                Rule::in(array_keys(config('council.plans'))),
            ],
        ]);

        try {
            $subscription = $request->user()->newSubscription(
                'membership',
                $this->planId($request->input('plan'))
            )->add();
        } catch (IncompletePayment $exception) {
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('subscription.edit')]
            );
        }

        if ($request->wantsJson()) {
            return Response::json($subscription);
        }

        return redirect()->route('subscription.edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('account.subscription', [
            'paymentMethods' => Auth::user()->paymentMethods(),
            'subscription' => Auth::user()->subscription('membership'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'plan' => [
                Rule::in(array_keys(config('council.plans'))),
            ],
            'active' => [
                'boolean',
            ],
        ]);

        $subscription = $request->user()->subscription('membership');

        if (
            $request->has('plan')
            && $this->planId($request->input('plan')) !== $subscription->stripe_plan
        ) {
            try {
                $subscription->swap($this->planId($request->input('plan')));
            } catch (IncompletePayment $exception) {
                return redirect()->route(
                    'cashier.payment',
                    [$exception->payment->id, 'redirect' => route('subscription.edit')]
                );
            }
        }

        if ($request->has('active')) {
            $active = $request->boolean('active');

            if ($active && $subscription->onGracePeriod()) {
                $subscription->resume();
            }

            if (! $active && ! $subscription->onGracePeriod()) {
                $subscription->cancel();
            }
        }

        if ($request->wantsJson()) {
            return Response::json($subscription);
        }

        return redirect()->route('subscription.edit');
    }

    /**
     * Return the Stripe plan ID.
     *
     * @param  string  $plan
     * @return string
     */
    protected function planId(string $plan): string
    {
        return config("council.plans.$plan");
    }
}
