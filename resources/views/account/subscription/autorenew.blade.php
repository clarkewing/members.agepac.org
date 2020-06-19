<h5 class="font-weight-bold pb-2 border-bottom mb-4">Renouvellement automatique</h5>

<div class="d-flex px-lg-4 mb-5">
    <p class="mb-0 mr-auto">
        @if($subscription->onGracePeriod())
            Tu as désactivé le renouvellement automatique de ta cotisation.<br>
            <span class="text-danger font-weight-bold">
                        Ton adhésion prendra fin le {{ $subscription->ends_at->format('j F Y') }}.
                    </span>
        @else
            Le renouvellement automatique est <span class="text-success font-weight-bold">activé</span>.<br>
            Ta prochaine cotisation sera prélevée le
            {{ Carbon\Carbon::createFromTimeStamp($subscription->asStripeSubscription()->current_period_end)->format('j F Y') }}.
        @endif
    </p>

    <form action="{{ route('subscription.update') }}" method="post">
        @csrf
        @method('patch')
        <input type="hidden" name="active" value="{{ $subscription->onGracePeriod() ? 1 : 0 }}">

        @if($subscription->onGracePeriod())
            <button type="submit" class="btn btn-outline-success ml-2">
                Activer
            </button>
        @else
            <button type="submit" class="btn btn-outline-danger ml-2">
                Désactiver
            </button>
        @endif
    </form>
</div>
