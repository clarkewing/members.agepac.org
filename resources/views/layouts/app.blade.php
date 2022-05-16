<x-base-layout>
    <div
        class="h-screen flex overflow-hidden bg-gray-100"
        x-data="{ sidebarOpen: false }"
        @keydown.window.escape="sidebarOpen = false"
    >
        <x-sidebar />

        <div x-init="$el.focus()" class="flex flex-col flex-1 overflow-auto focus:outline-none" tabindex="0">
            <x-jet-banner />

            <!-- Navigation Bar -->
            <div class="z-10 {{ isset($header) ? '' : 'shadow' }}">
                <livewire:navigation-menu />
            </div>

            <!-- Page Heading -->
            <div class="relative flex-none bg-white shadow">
                @if (isset($header))
                    <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                        <div class="py-6 lg:border-t lg:border-gray-200">
                            {{ $header }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 {{ $withContentPadding ? 'py-8' : '' }} overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('modals')
</x-base-layout>
