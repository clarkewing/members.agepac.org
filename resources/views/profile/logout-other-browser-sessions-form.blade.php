<x-jet-action-section>
    <x-slot name="title">
        Sessions
    </x-slot>

    <x-slot name="description">
        Gérez et fermez vos sessions actives sur d’autres navigateurs et appareils.
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            Si nécessaire, vous pouvez vous déconnecter de toutes vos autres sessions de navigation sur l’ensemble de
            vos appareils. Certaines de vos sessions récentes sont répertoriées ci-dessous&nbsp;; cependant, cette liste
            peut ne pas être exhaustive. Si vous pensez que votre compte a été compromis, vous devez également mettre à
            jour votre mot de passe.
        </div>

        @if (count($this->sessions) > 0)
            <div class="mt-5 space-y-6">
                <!-- Other Browser Sessions -->
                @foreach ($this->sessions as $session)
                    <div class="flex items-center">
                        <div>
                            @if ($session->agent->isDesktop())
                                <x-heroicon-o-desktop-computer class="w-8 h-8 text-gray-500" aria-hidden="true" />
                            @else
                                <x-heroicon-o-device-mobile class="w-8 h-8 text-gray-500" aria-hidden="true" />
                            @endif
                        </div>

                        <div class="ml-3">
                            <div class="text-sm text-gray-600">
                                {{ $session->agent->platform() }} - {{ $session->agent->browser() }}
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">
                                    {{ $session->ip_address }},

                                    @if ($session->is_current_device)
                                        <span class="text-green-500 font-semibold">Cet appareil</span>
                                    @else
                                        Dernière activité {{ $session->last_active }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex items-center mt-5">
            <x-button.primary class="w-auto" wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('Clôturer les autres sessions') }}
            </x-button.primary>

            <x-jet-action-message class="ml-3" on="loggedOut">
                {{ __('Terminé.') }}
            </x-jet-action-message>
        </div>

        <!-- Log Out Other Devices Confirmation Modal -->
        <x-jet-dialog-modal wire:model="confirmingLogout">
            <x-slot name="title">
                {{ __('Clôturer les autres sessions') }}
            </x-slot>

            <x-slot name="content">
                Veuillez saisir votre mot de passe pour confirmer que vous souhaitez vous déconnecter de vos autres
                sessions de navigation sur tous vos appareils.

                <div
                    class="mt-4 w-3/4"
                    x-data="{}"
                    x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)"
                >
                    <x-form.input
                        type="password"
                        name="password"
                        placeholder="Mot de passe"
                        x-ref="password"
                        wire:model.defer="password"
                        wire:keydown.enter="logoutOtherBrowserSessions"
                    />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary class="w-auto" wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                    Annuler
                </x-button.secondary>

                <x-button.primary class="w-auto ml-2"
                            wire:click="logoutOtherBrowserSessions"
                            wire:loading.attr="disabled">
                    {{ __('Clôturer les autres sessions') }}
                </x-button.primary>
            </x-slot>
        </x-jet-dialog-modal>
    </x-slot>
</x-jet-action-section>
