<div
    x-data="{ open: false }"
>
    <!-- Trigger -->
    {{-- TODO: Use slot attributes to replace with button element --}}
    <span x-on:click.stop="console.log('clicked'); open = true">
        {{ $button }}
    </span>

    <div
        x-show="open"
        style="display: none;"
        x-on:keydown.escape.prevent.stop="open = false"
        class="relative z-40"
        aria-modal="true"
        {{ $attributes->merge(['role' => 'dialog']) }}
    >
        <!-- Overlay -->
        <div
            x-show="open"
            x-transition.opacity.duration.300ms
            x-on:click="open = false"
            class="fixed inset-0 bg-gray-600 bg-opacity-75"
        ></div>

        <div class="fixed inset-0 flex z-40">
            <!-- Slide-over -->
            <div
                x-show="open"
                x-transition:enter="transition ease-in-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                x-on:click.outside="open = false"
                x-trap.noscroll.inert="open"
                {{ $attributes->class([
                    'relative flex-1 flex flex-col w-full',
                    'max-w-xs' => ! Str::contains($attributes->get('class'), 'max-w-'),
                    'bg-white' => ! Str::contains($attributes->get('class'), 'bg-'),
                ]) }}
            >
                <!-- Close button -->
                <div
                    x-show="open"
                    x-transition.opacity.duration.300ms
                    class="absolute top-0 right-0 -mr-12 pt-2"
                >
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                    >
                        <span class="sr-only">Close sidebar</span>
                        <x-heroicon-o-x-mark class="h-6 w-6 text-white" aria-hidden="true"/>
                    </button>
                </div>

                <!-- Start panel -->
                {{ $slot }}
                <!-- End panel -->
            </div>

            <div class="flex-shrink-0 w-14">
                <!-- Force sidebar to shrink to fit close icon -->
            </div>
        </div>
    </div>
</div>
