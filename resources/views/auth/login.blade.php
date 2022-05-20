<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Connecte-toi à ton compte</h2>
        <p class="mt-2 text-sm text-gray-600">
            Ou
            <a
                href="{{ route('register') }}"
                class="font-medium text-wedgewood-600 hover:text-wedgewood-500"
            >
                crée ton compte
            </a>
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

            <div class="text-sm">
                <a
                    href="{{ route('password.request') }}"
                    class="font-medium text-wedgewood-600 hover:text-wedgewood-500"
                >
                    Mot de passe oublié ?
                </a>
            </div>
        </div>

        <x-button.primary type="submit">Connexion</x-button.primary>
    </form>
</x-auth-form-layout>
