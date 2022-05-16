<x-base-layout class="min-h-screen bg-white flex flex-col text-gray-900" :title="$title">
    <div class="min-h-screen bg-white flex flex-col">
        <div class="relative z-40 bg-gray-900 pb-6">
            <x-guest-navigation />
        </div>

        <main class="flex-1 bg-white flex">
            <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
                <div class="mx-auto w-full max-w-sm lg:w-96">
                    {{ $slot }}
                </div>
            </div>
            <div class="hidden lg:block relative w-0 flex-1">
                <img
                    class="absolute inset-0 h-full w-full object-cover"
                    src="{{ asset('media/pa31-bed-of-clouds.jpg') }}"
                    alt="Dusk seen from PA31 cockpit over a bed of clouds"
                />
            </div>
        </main>
    </div>

    <x-footer />
</x-base-layout>
