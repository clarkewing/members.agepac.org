@extends('layouts.form-with-bg')

@section('card-body')
<form class="mb-0" method="POST" action="{{ route('login') }}">
    @csrf

    <h2 class="text-center mb-4">{{ __('Connexion') }}</h2>

    <div class="form-group">
        <label for="email" class="sr-only">{{ __('Adresse email') }}</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('Adresse email') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="sr-only">{{ __('Mot de passe') }}</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Mot de passe') }}" required autocomplete="current-password">

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group text-center">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                {{ __('Se souvenir de moi') }}
            </label>
        </div>
    </div>

    <div class="form-group mb-1">
        <button type="submit" class="btn btn-primary btn-block">
            {{ __('Connexion') }}
        </button>
    </div>

    @if (Route::has('password.request'))
        <div class="form-group text-center mb-0">
            <a class="btn btn-link text-secondary" href="{{ route('password.request') }}">
                {{ __('Mot de passe oublié ?') }}
            </a>
        </div>
    @endif
</form>
@endsection
