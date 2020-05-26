<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Laravel\Cashier\Exceptions\InvalidPaymentMethod;

class PaymentMethodsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function create(Request $request)
    {
        return [
            'intent' => $request->user()->createSetupIntent(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);

        $user = $request->user();
        $user->createOrGetStripeCustomer(); // Ensure user is customer.

        if ($user->hasPaymentMethod()) {
            $paymentMethod = $user->addPaymentMethod(
                $request->input('payment_method')
            );
        } else {
            $paymentMethod = $user->updateDefaultPaymentMethod(
                $request->input('payment_method')
            );
        }

        return Response::json($paymentMethod->asStripePaymentMethod(), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $paymentMethodId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $paymentMethodId)
    {
        $request->validate([
            'default' => 'boolean',
        ]);

        $paymentMethod = $this->findPaymentMethod($paymentMethodId);

        if ($request->input('default')) {
            Auth::user()->updateDefaultPaymentMethod($paymentMethod->id);
        }

        return Response::make('', 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $paymentMethodId
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $paymentMethodId)
    {
        $paymentMethod = $this->findPaymentMethod($paymentMethodId);

        abort_if(
            $paymentMethod->id === optional(Auth::user()->defaultPaymentMethod())->id,
            422
        );

        $paymentMethod->delete();

        return Response::make('', 204);
    }

    /**
     * Find the payment method from its identifier
     * and ensure it belongs to the authenticated user.
     *
     * @param  string  $paymentMethodId
     * @return \Laravel\Cashier\PaymentMethod
     */
    protected function findPaymentMethod(string $paymentMethodId)
    {
        try {
            return Auth::user()->findPaymentMethod($paymentMethodId);
        } catch (InvalidPaymentMethod $e) {
            abort(403);
        }
    }
}
