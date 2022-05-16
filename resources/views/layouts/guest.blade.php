<x-base-layout {{ $attributes->merge(['class' => 'text-gray-900']) }} :title="$title">
    <div class="relative z-40 bg-gray-900 pb-6">
        <x-guest-navigation />
    </div>

    <main>
        {{ $slot }}
    </main>

    <x-footer />
</x-base-layout>
