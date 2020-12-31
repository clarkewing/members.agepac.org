@if(! Auth::user()->subscribed('default'))
    <div class="alert alert-danger mb-5" role="alert">
        <h4 class="alert-heading">Ta cotisation n’est pas à jour.</h4>
        <p>Rejoins l’AGEPAC ci-dessous afin d’accéder à toutes les fonctionalités du site.</p>
        <hr>
        <p class="mb-0">
            Si tu as des questions, n’hésite pas à <a href="{{ route('pages.show', 'contact') }}" class="alert-link">nous contacter</a>.
        </p>
    </div>

@elseif(! Auth::user()->subscription('default')->onGracePeriod() && ! Auth::user()->hasDefaultPaymentMethod())
    <div class="alert alert-warning mb-5" role="alert">
        <h4 class="alert-heading">Nous ne pourrons pas renouveller ta cotisation.</h4>
        <p>
            Aucun moyen de paiement n’est enregistré pour ton compte.<br>
            <a href="{{ route('subscription.edit') }}" class="alert-link">Ajoute un moyen de paiement</a> pour continuer à profiter de ton adhésion.
        </p>
        <hr>
        <p class="mb-0">
            Si tu as des questions, n’hésite pas à <a href="{{ route('pages.show', 'contact') }}" class="alert-link">nous contacter</a>.
        </p>
    </div>

@endif
