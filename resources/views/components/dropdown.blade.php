@props(['origin' => 'left'])

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('dropdown', () => ({
                    open: false,

                    toggle() {
                        if (this.open) {
                            return this.close()
                        }

                        this.$refs.button.focus()

                        this.open = true
                    },

                    close(focusAfter) {
                        if (! this.open) return

                        this.open = false

                        focusAfter && focusAfter.focus()
                    }
                }))
            })
        </script>
    @endpush
@endonce

<div {{ $attributes->class(['flex justify-center']) }}>
    <div
        x-data="dropdown"
        x-on:keydown.escape.prevent.stop="close($refs.button)"
        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
        x-id="['dropdown']"
        class="relative"
    >
        <!-- Button -->
        <button
            x-ref="button"
            x-on:click="toggle()"
            :aria-expanded="open"
            :aria-controls="$id('dropdown')"
            type="button"
            class="flex"
            {{-- TODO: Implement slot attributes. --}}
        >
            {{ $button }}
        </button>

        <!-- Panel -->
        <div
            x-ref="panel"
            x-show="open"
            x-transition.origin.top.{{ $origin }}
            x-on:click.outside="close($refs.button)"
            :id="$id('dropdown')"
            style="display: none;"
            {{ $attributes->class([
                'absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 focus:outline-none',
                'left-0' => $origin === 'left',
                'right-0' => $origin === 'right',
            ]) }}
            role="menu"
            aria-orientation="vertical"
        >
            {{-- TODO: Implement slot attributes. --}}
            {{ $slot }}
        </div>
    </div>
</div>
