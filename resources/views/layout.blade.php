<x-base-layout class="h-full">
    <div class="min-h-screen bg-gray-100">
        <header class="sticky top-0 z-30 bg-white shadow-sm">
{{--            sticky top-0 z-40 w-full backdrop-blur flex-none transition-colors duration-500 lg:z-50--}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative flex justify-between lg:gap-8">
                    <div class="flex md:absolute md:left-0 md:inset-y-0 lg:static lg:w-60">
                        <div class="shrink-0 flex items-center w-full">
                            <a href="{{ route('home') }}">
                                <x-application-mark class="lg:hidden h-6 w-auto" />
                                <x-application-logo class="hidden lg:block h-auto w-full" />
                            </a>
                        </div>
                    </div>
                    <div class="flex-1 flex justify-between xl:grid xl:grid-cols-12 lg:gap-8">
                        <div class="min-w-0 flex-1 md:px-20 lg:px-0 xl:col-span-8">
                            <div class="flex items-center px-6 py-4 md:max-w-3xl md:mx-auto lg:max-w-none lg:mx-0 lg:px-0">
                                <x-quick-search />
                            </div>
                        </div>

                        <div class="hidden lg:flex lg:items-center lg:justify-end xl:col-span-4 lg:space-x-5">
                            <a href="#" class="shrink-0 bg-white rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500">
                                <span class="sr-only">View notifications</span>
                                <x-heroicon-o-bell class="h-6 w-6" aria-hidden="true" />
                            </a>

                            <!-- Profile dropdown -->
                            <x-dropdown class="shrink-0" origin="right">
                                <x-slot name="button">
                                    <div class="bg-white rounded-full flex focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500">
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                    </div>
                                </x-slot>

                                <x-account-dropdown/>
                            </x-dropdown>

                            <div class="pl-1">
                                <x-button href="#">New Post</x-button>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center md:absolute md:right-0 md:inset-y-0 lg:hidden">
                        <x-slide-over role="navigation">
                            <x-slot name="button">
                                <button type="button" class="-mx-2 rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-wedgewood-500" aria-expanded="false">
                                    <span class="sr-only">Open menu</span>
                                    <x-heroicon-o-menu class="h-6 w-6" aria-hidden="true" />
                                </button>
                            </x-slot>

                            <x-off-canvas-nav />
                        </x-slide-over>
                    </div>
                </div>
            </div>
        </header>

        <div class="py-6">
            <div class="flex justify-between mx-auto sm:px-6 lg:max-w-7xl lg:px-8 lg:gap-8">
                <div class="hidden lg:block lg:w-60">
                    <nav aria-label="Sidebar" class="sticky top-[5.875rem]">
                        <x-sidebar-nav/>

                        <div class="mt-8">
                            <h3
                                class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                id="headlines"
                            >
                                Actualités
                            </h3>
                            <div class="mt-1 space-y-1" aria-labelledby="headlines">
                                <x-sidebar-nav-item href="#">
                                    Lancement site public
                                </x-sidebar-nav-item>

                                <x-sidebar-nav-item href="#">
                                    Réunion de Bureau - Mai 2022
                                </x-sidebar-nav-item>

                                <x-sidebar-nav-item href="#">
                                    Séances cinéma Top Gun Maverick
                                </x-sidebar-nav-item>

                                <x-sidebar-nav-item href="#">
                                    Réunion de Bureau - Mai 2022
                                </x-sidebar-nav-item>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- Main wrapper & right aside -->
                <div class="flex-1 flex flex-col-reverse gap-6 lg:gap-8 md:grid md:grid-cols-12">
                    <main class="md:col-span-8">
                        <!-- Start main area-->
                        <div class="relative h-full" style="min-height: 96rem">
                            <div class="absolute inset-0 border-2 border-gray-200 border-dashed rounded-lg"></div>
                        </div>
                        <!-- End main area -->
                    </main>
                    <aside class="md:col-span-4">
                        <div class="sticky top-[5.875rem] space-y-4">
                            <!-- Start right column area -->
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
</x-base-layout>
