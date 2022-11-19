<x-app-layout :aside-on-top="false">
    <livewire:feed />

    <x-slot name="aside">
        <div class="grid grid-cols-1 gap-6">
            <x-announcements-card/>

            <x-new-members-card />
        </div>
    </x-slot>
</x-app-layout>
