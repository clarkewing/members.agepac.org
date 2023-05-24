<div class="w-full">
    <label for="search" class="sr-only">Quick search</label>
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
            <x-heroicon-s-magnifying-glass class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </div>
        <input
            type="search"
            id="search"
            name="search"
            class="block w-full bg-white border border-gray-300 rounded-md py-2 pl-10 pr-3 lg:pr-12 text-sm placeholder-gray-500 focus:outline-none focus:text-gray-900 focus:placeholder-gray-400 focus:ring-1 focus:ring-wedgewood-500 focus:border-wedgewood-500"
            placeholder="Search"
            x-data="{
                handleKeydown(e) {
                    if (this.$root === document.activeElement) return

                    if (e.key === '/' || e.key === 'k' && e.metaKey) {
                        e.preventDefault()
                        this.$root.focus()
                    }
                },
            }"
            x-on:keydown.window="handleKeydown"
            x-on:keydown.escape="$root.blur()"
        />
        <div class="hidden lg:block absolute inset-y-0 right-0 flex py-1.5 pr-1.5">
            <kbd class="inline-flex items-center border border-gray-200 rounded h-full px-2 text-sm font-sans font-medium text-gray-400">⌘K</kbd>
        </div>
    </div>
</div>

