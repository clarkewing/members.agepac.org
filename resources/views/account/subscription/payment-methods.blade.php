<h5 class="font-weight-bold pb-2 border-bottom mb-4">Moyens de paiement</h5>

<div class="px-lg-4 mb-5">
    @foreach($paymentMethods as $paymentMethod)
        <payment-method :payment-method="{{ json_encode($paymentMethod->asStripePaymentMethod()) }}"
                        class="mb-3"></payment-method>
    @endforeach

    <new-payment-method btn-class="btn-block border-placeholder p-4 text-center text-muted">
        <template #button>
            <svg class="bi bi-credit-card mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                 xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M14 3H2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2z"/>
                <rect width="3" height="3" x="2" y="9" rx="1"/>
                <path d="M1 5h14v2H1z"/>
            </svg>
            Ajouter un moyen de paiement
        </template>
    </new-payment-method>
</div>
