<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <div id="registrationHeader">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Migration de tes données</h2>
            <p class="mt-2 text-sm text-gray-600">
                Vérifie tes données pour migrer ton compte vers notre nouveau site.
            </p>
        </div>
    </x-slot>

    <livewire:migrate />
</x-auth-form-layout>
