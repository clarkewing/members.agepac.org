<x-jet-action-section>
    <x-slot name="title">
        Authentification à deux facteurs
    </x-slot>

    <x-slot name="description">
        Ajoutez une sécurité supplémentaire à votre compte grâce à l’authentification à deux facteurs.
    </x-slot>

    <x-slot name="content">
        <h3 class="flex items-center text-lg font-medium {{ $this->enabled ? 'text-green-600' : 'text-red-600' }}">
            @if ($this->enabled)
                <x-heroicon-o-lock-closed class="h-5 w-5 mr-2" aria-hidden="true" />
                Vous avez activé l’authentification à deux facteurs.
            @else
                <x-heroicon-o-lock-open class="h-5 w-5 mr-2" aria-hidden="true" />
                Vous n’avez pas activé l’authentification à deux facteurs.
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600">
            <p>
                Lorsque l’authentification à deux facteurs est activée, vous serez invité à saisir un token aléatoire
                sécurisé lors de l’authentification. Vous pouvez récupérer ce token depuis l’application Google
                Authenticator de votre téléphone.
            </p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        L’authentification à deux facteurs est désormais activée Scannez le code QR suivant à l’aide de
                        l’application d’authentification de votre téléphone.
                    </p>
                </div>

                <div class="mt-4">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        Stockez ces codes de récupération dans un gestionnaire de mots de passe sécurisé. Ils peuvent
                        être utilisés pour récupérer l’accès à votre compte en cas de perte de votre appareil
                        d’authentification à deux facteurs.
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <x-jet-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button.primary class="w-auto" wire:loading.attr="disabled">
                        Activer
                    </x-button.primary>
                </x-jet-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-jet-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-button.secondary class="w-auto mr-3">
                            Régénérer les codes de récupération
                        </x-button.secondary>
                    </x-jet-confirms-password>
                @else
                    <x-jet-confirms-password wire:then="showRecoveryCodes">
                        <x-button.secondary class="w-auto mr-3">
                            Afficher les codes de récupération
                        </x-button.secondary>
                    </x-jet-confirms-password>
                @endif

                <x-jet-confirms-password wire:then="disableTwoFactorAuthentication">
                    <x-button.secondary class="w-auto" wire:loading.attr="disabled">
                        Désactiver
                    </x-button.secondary>
                </x-jet-confirms-password>
            @endif
        </div>
    </x-slot>
</x-jet-action-section>
