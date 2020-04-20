@extends('layouts.form-with-bg')

@section('card-body')
<h2 class="text-center mb-4">{{ __('Vérifiez votre adresse email') }}</h2>

@if (session('resent'))
    <div class="alert alert-success" role="alert">
        {{ __('Un nouveau lien de vérification vous a été envoyé.') }}
    </div>
@endif

<form class="mb-0" method="POST" action="{{ route('verification.resend') }}">
    @csrf
    <p>{{ __('Avant de procéder, veuillez vérifier que vous n\'avez pas déjà recu un email avec un lien de vérification.') }}</p>
    <p class="mb-0">
        {{ __('Si vous n\'avez pas reçu l\'email') }},
        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('cliquez ici') }}</button>
        {{ __('pour en demander le renvoi') }}.
    </p>
</form>
@endsection
