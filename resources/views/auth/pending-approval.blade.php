<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <h2 class="mt-6 text-2xl font-extrabold text-gray-900">{{ __('Ton compte doit être approuvé.') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('Ceci prend généralement moins de 24 heures.') }}<br>
            {{ __('Tu recevras un email une fois les vérifications nécessaires effectuées.') }}
        </p>
    </x-slot>

    <x-logout-form>
        <x-form.submit-secondary>{{ __('Log Out') }}</x-form.submit-secondary>
    </x-logout-form>
</x-auth-form-layout>
