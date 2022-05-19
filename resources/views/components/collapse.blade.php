@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('collapse', () => ({
                    init() {
                        this.$nextTick(() => {
                            if (this.isIsolated()) return

                            this.id = Array.prototype.slice.call(this.$root.parentNode.children).indexOf(this.$root)
                        })
                    },

                    id: null,

                    isExpanded: false,

                    get expanded() {
                        if (this.isIsolated()) return this.isExpanded

                        return this.active === this.id
                    },

                    set expanded(value) {
                        if (this.isIsolated()) return this.isExpanded = value

                        this.active = value ? this.id : null
                    },

                    isIsolated() {
                        return typeof this.active === 'undefined'
                    },
                }))
            })
        </script>
    @endpush
@endonce

<div
    x-data="collapse"
    x-id="['collapse']"
    role="region"
    {{ $attributes }}
>
    <span
        x-on:click="expanded = ! expanded"
        aria-haspopup="true"
        :aria-expanded="expanded"
        :aria-controls="$id('collapse')"
        {{-- TODO: Implement slot class --}}
    >
        {{ $button }}
    </span>

    <div
        x-show="expanded"
        x-collapse
        :id="$id('collapse')"
        role="menu"
        style="display: none; height: 0; overflow: hidden;"
        {{-- TODO: Implement slot class --}}
        class="mt-1 space-y-1"
    >
        {{ $slot }}
    </div>
</div>
