<x-base-layout class="h-full">
    <div class="min-h-screen bg-gray-100">
        <x-layout.header/>

        <div class="py-6">
            <div class="flex justify-between mx-auto sm:px-6 lg:max-w-7xl lg:px-8 lg:gap-8">
                <div class="hidden lg:block lg:w-60">
                    <nav aria-label="Sidebar" class="sticky top-[5.875rem]">
                        @isset($sidebar)
                            {{ $sidebar }}
                        @else
                            <x-sidebar-nav/>

                            <x-sidebar-headlines class="mt-8" />
                        @endif
                    </nav>
                </div>

                <!-- Main wrapper & right aside -->
                <div class="flex-1 flex {{ $asideOnTop ? 'flex-col-reverse' : 'flex-col' }} gap-6 lg:gap-8 md:grid md:grid-cols-12">
                    <main class="{{ isset($aside) ? 'md:col-span-8' : 'md:col-span-12' }}">
                        <!-- Start main area-->
                        {{ $slot }}
                        <!-- End main area -->
                    </main>

                    @isset($aside)
                        <aside class="md:col-span-4">
                            <div class="sticky top-[5.875rem] space-y-4">
                                <!-- Start right column area -->
                                {{ $aside }}
                                <!-- End right column area -->
                            </div>
                        </aside>
                    @endisset
                </div>

            </div>
        </div>
    </div>

    <x-layout.footer/>
</x-base-layout>
