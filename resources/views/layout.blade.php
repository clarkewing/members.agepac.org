<x-base-layout class="h-full">
    <div class="min-h-screen bg-gray-100">
        <x-layout.header/>

        <div class="py-6">
            <div class="flex justify-between mx-auto sm:px-6 lg:max-w-7xl lg:px-8 lg:gap-8">
                <div class="hidden lg:block lg:w-60">
                    <nav aria-label="Sidebar" class="sticky top-[5.875rem]">
                        <x-sidebar-nav/>

                        <x-sidebar-headlines class="mt-8" />
                    </nav>
                </div>

                <!-- Main wrapper & right aside -->
                <div class="flex-1 flex flex-col-reverse gap-6 lg:gap-8 md:grid md:grid-cols-12">
                    <main class="md:col-span-8">
                        <!-- Start main area-->
{{--                        {{ $slot }}--}}
                        <div class="relative h-full" style="min-height: 36rem">
                            <div class="absolute inset-0 border-2 border-gray-200 border-dashed rounded-lg"></div>
                        </div>
                        <!-- End main area -->
                    </main>
                    <aside class="md:col-span-4">
                        <div class="sticky top-[5.875rem] space-y-4">
                            <!-- Start right column area -->
{{--                            {{ $aside }}--}}
                            <div class="h-full relative" style="min-height: 16rem">
                                <div class="absolute inset-0 border-2 border-gray-200 border-dashed rounded-lg"></div>
                            </div>
                            <!-- End right column area -->
                        </div>
                    </aside>
                </div>

            </div>
        </div>
    </div>

    <x-layout.footer/>
</x-base-layout>
