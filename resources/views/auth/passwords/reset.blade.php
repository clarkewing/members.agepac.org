@extends('layouts.form-with-bg')

@section('card-body')
<form class="mb-0" method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <h2 class="text-center mb-4">{{ __('Réinitialisation de mot de passe') }}</h2>

    <div class="form-group">
        <label for="email" class="sr-only">{{ __('Adresse email') }}</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('Adresse email') }}" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="sr-only">{{ __('Mot de passe') }}</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Mot de passe') }}" required autocomplete="new-password">

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password-confirm" class="sr-only">{{ __('Confirmer mot de passe') }}</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirmer mot de passe') }}" required autocomplete="new-password">
    </div>

    <div class="form-group mb-1">
        <button type="submit" class="btn btn-primary btn-block">
            {{ __('Réinitialiser') }}
        </button>
    </div>
</form>
@endsection
