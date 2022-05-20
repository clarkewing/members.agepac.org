<x-auth-form-layout>
    <x-slot name="header" id="form-header">
        <x-application-mark class="h-10 w-auto" />
    </x-slot>

    <div x-data="{ recovery: false }">
        <template x-teleport="#form-header">
            <p class="mt-6 text-sm text-gray-600" x-show="! recovery">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </p>

            <p class="mt-6 text-sm text-gray-600" x-show="recovery">
                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            </p>
        </template>

        <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-6">
            @csrf

            <x-form.input
                x-ref="code"
                x-show="! recovery"
                label="{{ __('Code') }}"
                name="code"
                inputmode="numeric"
                autocomplete="one-time-code"
                autofocus
            />

            <x-form.input
                x-ref="recovery_code"
                x-show="recovery"
                label="{{ __('Recovery Code') }}"
                name="recovery_code"
                autocomplete="one-time-code"
            />

            <div class="flex items-center justify-end mt-4">
                <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                        x-show="! recovery"
                        x-on:click="
                                        recovery = true;
                                        $nextTick(() => { $refs.recovery_code.focus() })
                                    ">
                    {{ __('Use a recovery code') }}
                </button>

                <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                        x-show="recovery"
                        x-on:click="
                                        recovery = false;
                                        $nextTick(() => { $refs.code.focus() })
                                    ">
                    {{ __('Use an authentication code') }}
                </button>

                <x-button.primary type="submit">{{ __('Log in') }}</x-button.primary>
            </div>
        </form>
    </div>
</x-auth-form-layout>
