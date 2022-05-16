<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Sign in to your account</h2>
        <p class="mt-2 text-sm text-gray-600">
            Or
            <a
                href="{{ route('register') }}"
                class="font-medium text-wedgewood-600 hover:text-wedgewood-500"
            >
                create your account
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
            label="Email address"
            name="email"
            type="email"
            autocomplete="email"
            required
        />

        <x-form.input
            label="Password"
            name="password"
            type="password"
            autocomplete="current-password"
            required
        />

        <div class="flex items-center justify-between">
            <x-form.checkbox
                label="Remember me"
                name="remember"
            />

            <div class="text-sm">
                <a
                    href="{{ route('password.request') }}"
                    class="font-medium text-wedgewood-600 hover:text-wedgewood-500"
                >
                    Forgot your password?
                </a>
            </div>
        </div>

        <x-form.submit>Sign in</x-form.submit>
    </form>
</x-auth-form-layout>
