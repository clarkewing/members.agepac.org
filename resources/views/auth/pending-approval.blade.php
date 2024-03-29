@extends('layouts.form-with-bg')

@section('card-body')
    <h3 class="text-center mb-4">{{ __('Ton compte doit être approuvé.') }}</h3>

    <p class="text-center">
        {{ __('Ceci prend généralement moins de 24 heures.') }}<br>
        {{ __('Tu recevras un email une fois les vérifications nécessaires effectuées.') }}
    </p>

    <div class="text-center">
        <a class="btn btn-outline-info rounded-pill mx-auto" href="{{ route('pages.show', 'help') }}">
            <svg class="bi bi-life-preserver mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm0 1A8 8 0 108 0a8 8 0 000 16z"
                      clip-rule="evenodd"/>
                <path fill-rule="evenodd" d="M8 11a3 3 0 100-6 3 3 0 000 6zm0 1a4 4 0 100-8 4 4 0 000 8z"
                      clip-rule="evenodd"/>
                <path
                    d="M11.642 6.343L15 5v6l-3.358-1.343A3.99 3.99 0 0012 8a3.99 3.99 0 00-.358-1.657zM9.657 4.358L11 1H5l1.343 3.358A3.985 3.985 0 018 4c.59 0 1.152.128 1.657.358zM4.358 6.343L1 5v6l3.358-1.343A3.985 3.985 0 014 8c0-.59.128-1.152.358-1.657zm1.985 5.299L5 15h6l-1.343-3.358A3.984 3.984 0 018 12a3.99 3.99 0 01-1.657-.358z"/>
            </svg>
            <span>Aide</span>
        </a>
    </div>
@endsection
