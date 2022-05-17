<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">{{ __('Vérifiez votre adresse email') }}</h2>
        <p class="mt-6 text-sm text-gray-600">
            {{ __('Avant de procéder, veuillez vérifier que vous n\'avez pas déjà reçu un email avec un lien de vérification.') }}
        </p>
    </x-slot>

    @if (session('resent'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Un nouveau lien de vérification vous a été envoyé.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        {{ __('Si vous n\'avez pas reçu l\'email de vérification') }},
        <button type="submit" class="inline-flex font-medium text-wedgewood-600 hover:text-wedgewood-400">{{ __('cliquez ici') }}</button>
        {{ __('pour en demander le renvoi') }}.
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf

        <x-form.submit-secondary>{{ __('Log Out') }}</x-form.submit-secondary>
    </form>
</x-auth-form-layout>
