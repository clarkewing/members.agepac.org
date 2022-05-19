<x-app-layout :aside-on-top="false">
    <div class="px-4 sm:px-0">
        <x-tabs>
            <x-tab active>Récent</x-tab>
            <x-tab>Populaire</x-tab>
            <x-tab>Suivis</x-tab>
        </x-tabs>
    </div>

    <div class="mt-4">
        <h1 class="sr-only">Discussions récentes</h1>

        <ul role="list" class="space-y-4">
            @foreach(\App\Models\Thread::latest()->take(5)->get()->map(fn($thread) => $thread->posts()->latest()->first()) as $post)
                <li>
                    <x-post-card :post="$post"/>
                </li>
            @endforeach
        </ul>
    </div>

    <x-slot name="aside">
        <div class="grid grid-cols-1 gap-6">
            <x-announcements-card/>

            <x-new-members-card />
        </div>
    </x-slot>
</x-app-layout>
