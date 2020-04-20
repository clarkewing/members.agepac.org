@extends('layouts.form-with-bg')

@section('card-body')
<form class="mb-0" method="POST" action="{{ route('password.email') }}">
    @csrf

    <h3 class="text-center mb-4">{{ __('Réinitialisation de mot de passe') }}</h3>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="form-group">
        <label for="email" class="sr-only">{{ __('Adresse email') }}</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('Adresse email') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group mb-1">
        <button type="submit" class="btn btn-primary btn-block">
            {{ __('Envoyer lien de réinitialisation') }}
        </button>
    </div>
</form>
@endsection
