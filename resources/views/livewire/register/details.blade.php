<h3 class="text-xl font-bold mb-8">
    Quelques derniers détails...
</h3>

<form class="space-y-6" wire:submit.prevent="run">
    <x-form.input
        label="Date de naissance"
        type="date"
        name="birthdate"
        wire:model.defer="birthdate"
        autocomplete="bday"
        placeholder="JJ/MM/YYYY"
        required
    />

    <x-form.select
        label="Genre"
        name="gender"
        wire:model.defer="gender"
        :options="config('council.genders')"
        autocomplete="sex"
        required
    />

    <x-form.input
        label="Numéro de téléphone"
        help="Promis, on en abusera pas !"
        type="tel"
        name="phone"
        wire:model.defer="phone"
        autocomplete="tel"
        placeholder="+33 6 69 69 69 69"
        pattern="[\d+. -]+"
        required
    />

    <x-form.submit wire:loading.attr="disabled">
        <span>Vérification</span>
        <x-heroicon-s-arrow-right class="ml-2 h-4 w-4" aria-hidden="true" wire:loading.remove />
        <x-loading-indicator class="ml-2" wire:loading />
    </x-form.submit>
</form>
