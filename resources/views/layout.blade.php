<x-base-layout>
    <div class="min-h-screen bg-gray-100">
        <!--
          When the mobile menu is open, add `overflow-hidden` to the `body` element to prevent double scrollbars

          Open: "fixed inset-0 z-40 overflow-y-auto", Closed: ""
        -->
        <header class="bg-white shadow-sm lg:static lg:overflow-y-visible">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative flex justify-between lg:gap-8">
                    <div class="flex md:absolute md:left-0 md:inset-y-0 lg:static lg:w-60">
                        <div class="flex-shrink-0 flex items-center w-full">
                            <a href="{{ route('home') }}">
                                <x-application-mark class="lg:hidden h-6 w-auto" />
                                <x-application-logo class="hidden lg:block h-auto w-full" />
                            </a>
                        </div>
                    </div>
                    <div class="flex-1 flex justify-between xl:grid xl:grid-cols-12 lg:gap-8">
                        <div class="min-w-0 flex-1 md:px-20 lg:px-0 xl:col-span-8">
                            <div class="flex items-center px-6 py-4 md:max-w-3xl md:mx-auto lg:max-w-none lg:mx-0 lg:px-0">
                                <x-search-input />
{{--                                <div class="w-full">--}}
{{--                                    <label for="search" class="sr-only">Search</label>--}}
{{--                                    <div class="relative">--}}
{{--                                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">--}}
{{--                                            <x-heroicon-s-search class="h-5 w-5 text-gray-400" aria-hidden="true" />--}}
{{--                                        </div>--}}
{{--                                        <input id="search" name="search" class="block w-full bg-white border border-gray-300 rounded-md py-2 pl-10 pr-3 text-sm placeholder-gray-500 focus:outline-none focus:text-gray-900 focus:placeholder-gray-400 focus:ring-1 focus:ring-wedgewood-500 focus:border-wedgewood-500 sm:text-sm" placeholder="Search" type="search">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                        </div>

                        <div class="hidden lg:flex lg:items-center lg:justify-end xl:col-span-4 lg:space-x-5">
                            <a href="#" class="flex-shrink-0 bg-white rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500">
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
                                    <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>

                                    <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>

                                    <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                            </x-dropdown>

                            <div class="pl-1">
                                <x-button href="#">New Post</x-button>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center md:absolute md:right-0 md:inset-y-0 lg:hidden">
                        <!-- Mobile menu button -->
                        <button type="button" class="-mx-2 rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-wedgewood-500" aria-expanded="false">
                            <span class="sr-only">Open menu</span>
                            <!--
                              Icon when menu is closed.

                              Heroicon name: outline/menu

                              Menu open: "hidden", Menu closed: "block"
                            -->
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!--
                              Icon when menu is open.

                              Heroicon name: outline/x

                              Menu open: "block", Menu closed: "hidden"
                            -->
                            <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <nav class="lg:hidden" aria-label="Global">
                <div class="max-w-3xl mx-auto px-2 pt-2 pb-3 space-y-1 sm:px-4">
                    <!-- Current: "bg-gray-100 text-gray-900", Default: "hover:bg-gray-50" -->
                    <a href="#" aria-current="page" class="bg-gray-100 text-gray-900 block rounded-md py-2 px-3 text-base font-medium">Dashboard</a>

                    <a href="#" class="hover:bg-gray-50 block rounded-md py-2 px-3 text-base font-medium">Calendar</a>

                    <a href="#" class="hover:bg-gray-50 block rounded-md py-2 px-3 text-base font-medium">Teams</a>

                    <a href="#" class="hover:bg-gray-50 block rounded-md py-2 px-3 text-base font-medium">Directory</a>
                </div>
                <div class="border-t border-gray-200 pt-4 pb-3">
                    <div class="max-w-3xl mx-auto px-4 flex items-center sm:px-6">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">Chelsea Hagon</div>
                            <div class="text-sm font-medium text-gray-500">chelsea.hagon@example.com</div>
                        </div>
                        <button type="button" class="ml-auto flex-shrink-0 bg-white rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500">
                            <span class="sr-only">View notifications</span>
                            <!-- Heroicon name: outline/bell -->
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-3 max-w-3xl mx-auto px-2 space-y-1 sm:px-4">
                        <a href="#" class="block rounded-md py-2 px-3 text-base font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-900">Your Profile</a>

                        <a href="#" class="block rounded-md py-2 px-3 text-base font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-900">Settings</a>

                        <a href="#" class="block rounded-md py-2 px-3 text-base font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-900">Sign out</a>
                    </div>
                </div>
            </nav>
        </header>

        <div class="py-6">
            <div class="flex justify-between mx-auto sm:px-6 lg:max-w-7xl lg:px-8 lg:gap-8">
                <div class="hidden lg:block lg:w-60">
                    <nav aria-label="Sidebar" class="sticky top-6 divide-y divide-gray-300">
                        <!-- Start left column area -->
                        <div class="h-full relative" style="min-height: 12rem">
                            <div class="absolute inset-0 border-2 border-gray-200 border-dashed rounded-lg"></div>
                        </div>
                        <!-- End left column area -->
                    </nav>
                </div>

                <!-- Main wrapper & right aside -->
                <div class="flex-1 flex flex-col-reverse gap-6 lg:gap-8 md:grid md:grid-cols-12">
                    <main class="md:col-span-8">
                        <!-- Start main area-->
                        <div class="relative h-full" style="min-height: 36rem">
                            <div class="absolute inset-0 border-2 border-gray-200 border-dashed rounded-lg"></div>
                        </div>
                        <!-- End main area -->
                    </main>
                    <aside class="md:col-span-4">
                        <div class="sticky top-6 space-y-4">
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
