@unless(Auth::user()->subscribed('default'))
    <div class="alert alert-danger mb-5" role="alert">
        <h4 class="alert-heading">Ta cotisation n’est pas à jour.</h4>
        <p>Rejoins l’AGEPAC ci-dessous afin d’accéder à toutes les fonctionalités du site.</p>
        <hr>
        <p class="mb-0">
            Si tu as des questions, n’hésite pas à <a href="{{ route('pages.show', 'contact') }}" class="alert-link">nous contacter</a>.
        </p>
    </div>
@endunless
