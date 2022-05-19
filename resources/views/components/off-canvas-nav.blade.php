<div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
    <div class="flex-shrink-0 flex items-center px-4">
        <a href="{{ route('home') }}">
            <x-application-logo class="h-6 w-auto"/>
        </a>
    </div>
    <nav class="mt-5 px-2 space-y-1">
        <x-sidebar-nav/>
    </nav>
</div>
<div class="shrink-0 border-t border-gray-200 pt-4 pb-3">
    <div class="px-4 flex items-center">
        <a href="#" class="flex-1 group flex items-center">
            <div class="shrink-0">
                <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
            </div>
            <div class="ml-3 flex-1">
                <p class="text-base font-medium text-gray-700 group-hover:text-gray-900">Chelsea Hagon</p>
                <p class="text-sm font-medium text-gray-500 group-hover:text-gray-700">View profile</p>
            </div>
        </a>
        <button type="button" class="ml-3 shrink-0 bg-white rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500">
            <span class="sr-only">View notifications</span>
            <x-heroicon-o-bell class="h-6 w-6" aria-hidden="true"/>
        </button>
    </div>
    <div class="mt-3 px-2 space-y-1">
        <x-sidebar-nav-item
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Account settings
        </x-sidebar-nav-item>

        <x-logout-form>
            <x-sidebar-nav-item type="submit">
                Sign out
            </x-sidebar-nav-item>
        </x-logout-form>
    </div>
</div>
