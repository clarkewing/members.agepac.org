<x-jet-form-section submit="updatePassword">
    <x-slot name="title">
        Modifier mot de passe
    </x-slot>

    <x-slot name="description">
        En ce qui concerne le mot de passe, sois {{ $this->user->gender === 'F' ? 'futée' : 'futé' }},
        choisis quelque chose de super mega secret et sûr.<br>
        Voici
        <a href="https://xkcd.com/936/" target="_blank" title="XKCD Password Strength" class="text-wedgewood-500 hover:underline">une petite BD</a>
        trop chouette pour t'aider à choisir.
    </x-slot>

    <x-slot name="form">
        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-3 sm:col-span-2">
                <x-form.input
                    label="Mot de passe actuel"
                    name="password"
                    type="password"
                    wire:model.defer="state.current_password"
                    autocomplete="current-password"
                    required
                />
            </div>

            <div class="col-span-3 sm:col-span-2">
                <x-form.input
                    label="Nouveau mot de passe"
                    name="password"
                    type="password"
                    wire:model.defer="state.password"
                    autocomplete="new-password"
                    required
                />
            </div>

            <div class="col-span-3 sm:col-span-2">
                <x-form.input
                    label="Confirmation mot de passe"
                    name="password_confirmation"
                    type="password"
                    wire:model.defer="state.password_confirmation"
                    autocomplete="new-password"
                    required
                />
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            Sauvegardé !
        </x-jet-action-message>

        <x-button.primary
            type="submit"
            class="w-auto"
            wire:loading.attr="disabled"
        >
            Sauvegarder
        </x-button.primary>
    </x-slot>
</x-jet-form-section>
