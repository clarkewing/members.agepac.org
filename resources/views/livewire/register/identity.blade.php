<h3 class="text-xl font-bold mb-8">
    Dis-nous en plus...
</h3>

<form class="space-y-6" wire:submit.prevent="run">
    <x-form.input
        label="Prénom"
        name="first_name"
        wire:model.defer="first_name"
        autocomplete="given-name"
        placeholder="Marc"
        required
    />

    <x-form.input
        label="Nom"
        name="last_name"
        wire:model.defer="last_name"
        autocomplete="family-name"
        placeholder="Houalla"
        required
    />

    <x-form.select
        label="Cursus"
        name="class_course"
        wire:model.defer="class_course"
        :options="config('council.courses')"
        required
    />

    <x-form.input
        label="Promotion"
        name="class_year"
        wire:model.defer="class_year"
        type="number"
        min="1900" max="2199" step="1"
        required
    />

    <x-form.submit class="mt-6" wire:loading.attr="disabled">
        <span>Continuer</span>
        <x-heroicon-s-arrow-right class="ml-2 h-4 w-4" aria-hidden="true" wire:loading.remove />
        <x-loading-indicator class="ml-2" wire:loading />
    </x-form.submit>
</form>
