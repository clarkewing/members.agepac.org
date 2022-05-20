<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <p class="mt-6 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
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
            label="Email address"
            name="email"
            type="email"
            autocomplete="email"
            required
            autofocus
        />

        <div class="space-y-4">
            <x-button.primary type="submit">{{ __('Email Password Reset Link') }}</x-button.primary>

            <x-button.secondary href="{{ route('login') }}">
                <x-heroicon-s-arrow-left class="mr-2 h-4 w-4" aria-hidden="true" />
                <span>{{ __('Go back') }}</span>
            </x-button.secondary>
        </div>
    </form>
</x-auth-form-layout>
