<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <p class="mt-6 text-sm text-gray-600">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </x-slot>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <x-form.submit>{{ __('Resend Verification Email') }}</x-form.submit>
    </form>

    <x-logout-form class="mt-4">
        <x-form.submit-secondary>{{ __('Log Out') }}</x-form.submit-secondary>
    </x-logout-form>
</x-auth-form-layout>
