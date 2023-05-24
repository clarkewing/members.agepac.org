<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Connecte-toi à ton compte</h2>
        <p class="mt-2 text-sm text-gray-600">
            Ou
            <x-link.primary href="{{ route('register') }}">crée ton compte</x-link.primary>
        </p>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <x-form.input
            label="Adresse email"
            name="email"
            type="email"
            autocomplete="email"
            required
            autofocus
        />

        <x-form.input
            label="Mot de passe"
            name="password"
            type="password"
            autocomplete="current-password"
            required
        />

        <div class="flex items-center justify-between">
            <x-form.checkbox
                label="Rester connecté"
                name="remember"
            />

            <x-link.primary
                href="{{ route('password.request') }}"
                class="text-sm"
            >
                Mot de passe oublié ?
            </x-link.primary>
        </div>

        <x-button.primary type="submit">Connexion</x-button.primary>
    </form>

    @env('local')
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500"> Or continue with </span>
                </div>
            </div>

            <div class="mt-6">
                <div>
                    <x-button.white href="{{ route('dev-login') }}">
                        <x-heroicon-o-code-bracket class="mr-2 h-5 w-5 text-gray-600" aria-hidden="true" />
                        Developer Account
                    </x-button.white>
                </div>
            </div>
        </div>
    @endenv
</x-auth-form-layout>
