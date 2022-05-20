<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <p class="mt-6 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
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
            label="Password"
            name="password"
            type="password"
            autocomplete="current-password"
            required
            autofocus
        />

        <x-button.primary type="submit">{{ __('Confirm') }}</x-button.primary>
    </form>
</x-auth-form-layout>
