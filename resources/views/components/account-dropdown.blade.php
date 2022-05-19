<div {{ $attributes->class(['divide-y divide-gray-100']) }}>
    <div class="px-4 pt-2 pb-3" role="none">
        <p class="text-sm" role="none">Signed in as</p>
        <p class="text-sm font-medium text-gray-900 truncate" role="none">tom@example.com</p>
    </div>
    <div class="py-1" role="none">
        <a href="#" class="block px-4 py-2 text-gray-700 text-sm hover:text-gray-900 hover:bg-gray-100" role="menuitem">Account
            settings</a>
        <a href="#" class="block px-4 py-2 text-gray-700 text-sm hover:text-gray-900 hover:bg-gray-100" role="menuitem">Support</a>
        <a href="#" class="block px-4 py-2 text-gray-700 text-sm hover:text-gray-900 hover:bg-gray-100" role="menuitem">License</a>
    </div>
    <div class="pt-1" role="none">
        <form method="POST" action="{{ route('logout') }}" role="none">
            <button type="submit"
                    class="w-full px-4 py-2 text-gray-700 text-left text-sm hover:text-gray-900 hover:bg-gray-100"
                    role="menuitem">Sign out
            </button>
        </form>
    </div>
</div>
