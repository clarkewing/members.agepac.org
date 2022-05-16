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

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-form.input
            label="Email address"
            name="email"
            type="email"
            :value="$request->email"
            required
        />

        <x-form.input
            label="Password"
            name="password"
            type="password"
            autocomplete="new-password"
            required
            autofocus
        />

        <x-form.input
            label="Confirm Password"
            name="password_confirmation"
            type="password"
            autocomplete="new-password"
            required
            autofocus
        />

        <x-form.submit>{{ __('Reset Password') }}</x-form.submit>
    </form>
</x-auth-form-layout>
