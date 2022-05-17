<form wire:submit.prevent="run">
    <x-form.input
        label="Comment t’appelles-tu ?"
        name="name"
        wire:model.defer="name"
        autocomplete="name"
        placeholder="Marc Houalla"
        required
        autofocus
    />

    <x-form.submit class="mt-6" wire:loading.attr="disabled">
        <span>Continuer</span>
        <x-heroicon-s-arrow-right class="ml-2 h-4 w-4" aria-hidden="true" wire:loading.remove />
        <x-loading-indicator class="ml-2" wire:loading />
    </x-form.submit>
</form>
