<h3 class="text-xl font-bold mb-8">
    Vérification
</h3>

<div class="space-y-6">
    <div>
        <x-dl-header title="Nom et promotion">
            <x-slot name="actions">
                <x-link.primary wire:click="goToStep('Identity')">
                    Modifier
                </x-link.primary>
            </x-slot>
        </x-dl-header>

        <x-dl class="mt-5 border-t border-gray-200">
            <x-dl-item title="Nom">
                {{ $name }}
            </x-dl-item>
            <x-dl-item title="Promotion">
                {{ $class_course }} {{ $class_year }}
            </x-dl-item>
        </x-dl>
    </div>

    <div>
        <x-dl-header title="Identifiants">
            <x-slot name="actions">
                <x-link.primary wire:click="goToStep('Credentials')">
                    Modifier
                </x-link.primary>
            </x-slot>
        </x-dl-header>

        <x-dl class="mt-5 border-t border-gray-200">
            <x-dl-item title="Adresse email">
                {{ $email }}
            </x-dl-item>
            <x-dl-item title="Mot de passe">
                &bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;
            </x-dl-item>
        </x-dl>
    </div>

    <div>
        <x-dl-header title="Détails supplémentaires">
            <x-slot name="actions">
                <x-link.primary wire:click="goToStep('Details')">
                    Modifier
                </x-link.primary>
            </x-slot>
        </x-dl-header>

        <x-dl class="mt-5 border-t border-gray-200">
            <x-dl-item title="Date de naissance">
                {{ \Illuminate\Support\Carbon::parse($birthdate)->isoFormat('D MMMM YYYY') }}
            </x-dl-item>
            <x-dl-item title="Genre">
                {{ config('council.genders')[$gender] }}
            </x-dl-item>
            <x-dl-item title="Numéro de téléphone">
                {{ $phone }}
            </x-dl-item>
        </x-dl>
    </div>
</div>

<form wire:submit.prevent="run">
    <x-form.submit class="mt-6" wire:loading.attr="disabled">
        <span>Terminé !</span>
        <x-heroicon-s-paper-airplane class="ml-2 h-4 w-4 rotate-45 -translate-y-0.5" aria-hidden="true" wire:loading.remove />
        <x-loading-indicator class="ml-2" wire:loading />
    </x-form.submit>
</form>
