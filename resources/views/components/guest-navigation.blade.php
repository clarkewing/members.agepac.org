<header
    class="relative"
    x-data="{
        show: false,
    }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
>
    <div class="pt-6">
        <nav class="relative max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6" aria-label="Global">
            <div class="flex items-center flex-1">
                <div class="flex items-center justify-between w-full md:w-auto">
                    <a href="{{ route('welcome') }}" class="group flex items-center">
                        <x-jet-application-mark class="h-10 w-auto sm:h-14 transform group-hover:-rotate-12 transition-transform duration-100 ease-in-out" />
                        <span class="ml-4 text-lg lg:text-xl text-white group-hover:text-gray-300 font-medium whitespace-nowrap">PixAir Survey</span>
                    </a>
                    <div class="-mr-2 flex items-center md:hidden">
                        <button
                            type="button"
                            x-on:click="show = true"
                            class="rounded-md p-2 inline-flex items-center justify-center text-gray-200 hover:text-gray-100 focus:outline-none focus:ring-2 focus-ring-inset focus:ring-white"
                            aria-expanded="false"
                        >
                            <span class="sr-only">Open main menu</span>
                            <x-heroicon-o-menu class="h-6 w-6" aria-hidden="true"/>
                        </button>
                    </div>
                </div>
                <div class="hidden space-x-6 lg:space-x-8 md:flex md:ml-8 lg:ml-10">
                    <x-flyout-menu width="xs">
                        <x-slot name="trigger">
                            <button
                                type="button"
                                class="group rounded inline-flex items-center text-base font-medium hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                x-bind:class="open ? 'text-gray-300' : 'text-white'"
                                @mousedown="if (! open) $event.preventDefault()" {{-- Prevent focus state on click --}}
                            >
                                <span>Company</span>

                                <x-heroicon-o-chevron-down
                                    class="ml-1 lg:ml-2 h-5 w-5 group-hover:text-gray-400"
                                    x-bind:class="open ? 'text-gray-400' : 'text-white'"
                                    aria-hidden="true"
                                />
                            </button>
                        </x-slot>

                        <x-flyout-menu-item
                            href="{{ route('about') }}"
                            heroicon="information-circle"
                        >
                            <x-slot name="title">About Us</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('team') }}"
                            heroicon="user-group"
                        >
                            <x-slot name="title">Our Team</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('fleet') }}"
                            heroicon="paper-airplane"
                        >
                            <x-slot name="title">Our Fleet</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('careers') }}"
                            heroicon="briefcase"
                        >
                            <x-slot name="title">Careers</x-slot>
                        </x-flyout-menu-item>
                    </x-flyout-menu>

                    <x-flyout-menu>
                        <x-slot name="trigger">
                            <button
                                type="button"
                                class="group rounded inline-flex items-center text-base font-medium hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                x-bind:class="open ? 'text-gray-300' : 'text-white'"
                                @mousedown="if (! open) $event.preventDefault()" {{-- Prevent focus state on click --}}
                            >
                                <span>Solutions</span>

                                <x-heroicon-o-chevron-down
                                    class="ml-1 lg:ml-2 h-5 w-5 group-hover:text-gray-400"
                                    x-bind:class="open ? 'text-gray-400' : 'text-white'"
                                    aria-hidden="true"
                                />
                            </button>
                        </x-slot>

                        <x-flyout-menu-item
                            href="{{ route('solutions.aerial-imaging') }}"
                            heroicon="camera"
                        >
                            <x-slot name="title">Aerial Imaging</x-slot>
                            <x-slot name="subtitle">Airborne imagery options including photography, infrared imaging, LiDAR, and more.</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('solutions.tv-radio-relay') }}"
                            heroicon="status-online"
                        >
                            <x-slot name="title">TV/Radio Relay</x-slot>
                            <x-slot name="subtitle">High fidelity broadcast coverage for worldwide events.</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('solutions.maritime-surveillance') }}"
                        >
                            <x-slot name="icon">
                                <svg
                                    class="flex-shrink-0 h-6 w-6 text-indigo-600"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.1,18.8
	c0,0,1.2-1.6,2.2-1.6c1.4,0,1.5,1.6,2.9,1.6c1.3,0,1.4-1.6,2.8-1.6c1.4,0,1.5,1.6,2.9,1.6c1.3,0,1.4-1.6,2.8-1.6s1.5,1.6,2.8,1.6
	c1.3,0,1.5-1.6,2.9-1.6c1,0,2.2,1.6,2.2,1.6 M22.8,13.4c0,0-1.3,1.1-3.7,1.1c-4.2,0-5.3-2.6-5.3-4.2c0-0.7,0.5-2.5,2-2.5
	c1.3,0,1.9,0.9,1.9,0.9c0.2,0.1,0.5-0.1,0.4-0.4c0,0-0.9-3-4.9-3c-5,0-6.1,4.3-7.5,6.2c-1.9,2.6-4.5,3-4.5,3"/>
                                </svg>
                            </x-slot>

                            <x-slot name="title">Maritime Surveillance</x-slot>
                            <x-slot name="subtitle">Long-range aircraft for low-height wildlife counting and border surveillance missions.</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('solutions.experimental-studies') }}"
                            heroicon="beaker"
                        >
                            <x-slot name="title">Experimental Studies</x-slot>
                            <x-slot name="subtitle">Versatile testbed aircraft to test your latest innovations on.</x-slot>
                        </x-flyout-menu-item>

                        <x-slot name="footer">
                            <div class="flex justify-center w-full">
                                <span class="text-base font-medium text-gray-900">
                                    Need a custom solution?
                                </span>
                                <a
                                    href="{{ route('contact') }}"
                                    class="ml-3 -m-2 p-2 flex items-center rounded-md text-base font-medium text-gray-900 hover:bg-gray-100 transition ease-in-out duration-150"
                                >
                                    <x-heroicon-o-phone class="flex-shrink-0 h-6 w-6 text-gray-400" aria-hidden="true" />
                                    <span class="ml-3">Let's talk</span>
                                </a>
                            </div>
                        </x-slot>
                    </x-flyout-menu>

                    <a
                        href="{{ route('blog') }}"
                        class="text-base font-medium text-white hover:text-gray-300"
                    >
                        Blog
                    </a>
                </div>
            </div>
            <div class="hidden md:flex md:items-center md:space-x-6">
                @guest
                    <a
                        href="{{ route('login') }}"
                        class="text-base font-medium text-white hover:text-gray-300"
                    >
                        Sign in
                    </a>
                    <a
                        href="{{ route('contact') }}"
                        class="hidden lg:inline-flex items-center px-4 py-2 rounded-md text-white font-medium bg-gray-600 hover:bg-gray-700"
                    >
                        Get in touch
                    </a>
                @elseauth
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-4 py-2 rounded-md text-white font-medium bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700"
                    >
                        Dashboard
                    </a>
                @endguest
            </div>
        </nav>
    </div>

    <!-- Mobile menu, show/hide based on menu open state. -->
    <div
        class="absolute z-10 top-0 inset-x-0 p-2 transition transform origin-top md:hidden"
        x-show="show"
        x-on:click.away="show = false"
        x-transition:enter="duration-150 ease-out"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="duration-100 ease-in"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="display: none;"
    >
        <div class="rounded-lg shadow-md bg-white ring-1 ring-black ring-opacity-5 overflow-hidden divide-y-2 divide-gray-50">
            <div class="pt-5 pb-6 px-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <x-jet-application-mark class="h-8 w-auto" />
                        <span class="ml-3 text-xl font-medium text-gray-900">PixAir Survey</span>
                    </div>
                    <div class="-mr-2">
                        <button
                            type="button"
                            x-on:click="show = false"
                            class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-600"
                        >
                            <span class="sr-only">Close menu</span>
                            <x-heroicon-o-x class="h-6 w-6" aria-hidden="true"/>
                        </button>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="grid gap-y-6">
                        <x-flyout-menu-item
                            href="{{ route('about') }}"
                            heroicon="information-circle"
                        >
                            <x-slot name="title">About Us</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('blog') }}"
                            heroicon="newspaper"
                        >
                            <x-slot name="title">Blog</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('solutions.aerial-imaging') }}"
                            heroicon="camera"
                        >
                            <x-slot name="title">Aerial Imaging</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('solutions.tv-radio-relay') }}"
                            heroicon="status-online"
                        >
                            <x-slot name="title">TV/Radio Relay</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('solutions.maritime-surveillance') }}"
                        >
                            <x-slot name="icon">
                                <svg
                                    class="flex-shrink-0 h-6 w-6 text-indigo-600"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.1,18.8
	c0,0,1.2-1.6,2.2-1.6c1.4,0,1.5,1.6,2.9,1.6c1.3,0,1.4-1.6,2.8-1.6c1.4,0,1.5,1.6,2.9,1.6c1.3,0,1.4-1.6,2.8-1.6s1.5,1.6,2.8,1.6
	c1.3,0,1.5-1.6,2.9-1.6c1,0,2.2,1.6,2.2,1.6 M22.8,13.4c0,0-1.3,1.1-3.7,1.1c-4.2,0-5.3-2.6-5.3-4.2c0-0.7,0.5-2.5,2-2.5
	c1.3,0,1.9,0.9,1.9,0.9c0.2,0.1,0.5-0.1,0.4-0.4c0,0-0.9-3-4.9-3c-5,0-6.1,4.3-7.5,6.2c-1.9,2.6-4.5,3-4.5,3"/>
                                </svg>
                            </x-slot>

                            <x-slot name="title">Maritime Surveillance</x-slot>
                        </x-flyout-menu-item>

                        <x-flyout-menu-item
                            href="{{ route('solutions.experimental-studies') }}"
                            heroicon="beaker"
                        >
                            <x-slot name="title">Experimental Studies</x-slot>
                        </x-flyout-menu-item>
                    </div>
                </div>
            </div>
            <div class="py-6 px-5 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('team') }}" class="text-base font-medium text-gray-900 hover:text-gray-700">
                        Our Team
                    </a>

                    <a href="{{ route('fleet') }}" class="text-base font-medium text-gray-900 hover:text-gray-700">
                        Our Fleet
                    </a>

                    <a href="{{ route('careers') }}" class="text-base font-medium text-gray-900 hover:text-gray-700">
                        Careers
                    </a>

                    <a href="#" class="text-base font-medium text-gray-900 hover:text-gray-700">
                        Contact
                    </a>
                </div>
                <div>
                    @guest
                        <a
                            href="{{ route('contact') }}"
                            class="block text-center w-full py-3 px-4 rounded-md shadow bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium hover:from-purple-700 hover:to-indigo-700"
                        >
                            Get in touch
                        </a>
                    @elseauth
                        <a
                            href="{{ route('dashboard') }}"
                            class="block text-center w-full py-3 px-4 rounded-md shadow bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium hover:from-purple-700 hover:to-indigo-700"
                        >
                            Dashboard
                        </a>
                    @endguest
                    <div class="mt-6">
                        @guest
                            <p class="text-center text-base font-medium text-gray-500">
                                Existing customer? <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500">Sign in</a>
                            </p>
                        @elseauth
                            <x-form :action="route('logout')">
                                <p class="text-center text-base font-medium text-gray-500">
                                    Not {{ Auth::user()->first_name }}?
                                    <a
                                        href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="text-indigo-600 hover:text-indigo-500"
                                    >
                                        Sign out
                                    </a>
                                </p>
                            </x-form>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
