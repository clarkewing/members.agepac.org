<div>
    <h3 class="text-lg leading-6 font-medium text-gray-900">Trouvé !</h3>
    <p class="mt-1 max-w-2xl text-sm text-gray-500">Vérifie que les informations ci-dessous te correspondent bien.</p>
</div>

<x-dl class="mt-5 border-t border-gray-200">
    <x-dl-item title="Prénom">
        {{ $first_name }}
    </x-dl-item>
    <x-dl-item title="Nom">
        {{ $last_name }}
    </x-dl-item>
    <x-dl-item title="Promotion">
        {{ $class_course }} {{ $class_year }}
    </x-dl-item>
</x-dl>

<div class="mt-6 space-y-3">
    <x-button wire:click="run">
        <span>C’est bien moi</span>
        <x-heroicon-s-arrow-right class="ml-2 h-4 w-4" aria-hidden="true" wire:loading.remove wire:target="run" />
        <x-loading-indicator class="ml-2" wire:loading wire:target="run" />
    </x-button>

    <x-button-secondary wire:click="resetIdentity">
        <span>Ce n’est pas moi</span>
        <x-loading-indicator class="ml-2" wire:loading wire:target="resetIdentity" />
    </x-button-secondary>
</div>
