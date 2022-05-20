<h3 class="text-xl font-bold mb-8">
    Identifiants
</h3>

<form class="space-y-6" wire:submit.prevent="run">
    <x-form.input
        label="Adresse email"
        help="Utilise ton adresse personnelle."
        type="email"
        name="email"
        wire:model.defer="email"
        autocomplete="email"
        placeholder="choucroute_loveur@example.com"
        required
    />

    <x-form.input
        label="Mot de passe"
        help="Choisis quelque chose de sûr et d’au moins 8 caractères."
        name="password"
        type="password"
        wire:model.defer="password"
        autocomplete="new-password"
        placeholder="Foy=Maison<3"
        required
    />

    <x-form.input
        label="Confirmation"
        name="password_confirmation"
        type="password"
        wire:model.defer="password_confirmation"
        autocomplete="new-password"
        required
    />

    <x-button.primary type="submit" class="mt-6" wire:loading.attr="disabled">
        <span>Continuer</span>
        <x-heroicon-s-arrow-right class="ml-2 h-4 w-4" aria-hidden="true" wire:loading.remove />
        <x-loading-indicator class="ml-2" wire:loading />
    </x-button.primary>
</form>
