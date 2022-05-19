<x-app-layout :aside-on-top="false">
    <div class="px-4 sm:px-0">
        <x-tabs>
            <x-tab active>Récent</x-tab>
            <x-tab>Populaire</x-tab>
            <x-tab>Suivis</x-tab>
        </x-tabs>
    </div>

    <x-feed.recent class="mt-4" />

    <x-slot name="aside">
        <div class="grid grid-cols-1 gap-6">
            <x-announcements-card/>

            <x-new-members-card />
        </div>
    </x-slot>
</x-app-layout>
