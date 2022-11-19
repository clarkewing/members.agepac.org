<div {{ $attributes->class(['divide-y divide-gray-100']) }}>
    <div class="px-4 pt-2 pb-3" role="none">
        <p class="text-sm" role="none">Connecté en tant que</p>
        <p class="text-sm font-medium text-gray-900 truncate" role="none">{{ Auth::user()->email }}</p>
    </div>
    <div class="py-1" role="none">
        <x-dropdown-item href="#">
            Profil
        </x-dropdown-item>
        <x-dropdown-item href="{{ route('account.info') }}">
            Informations
        </x-dropdown-item>
        <x-dropdown-item href="{{ route('account.security') }}">
            Sûreté
        </x-dropdown-item>
        <x-dropdown-item href="#">
            Cotisation
        </x-dropdown-item>
        <x-dropdown-item href="#">
            Aide
        </x-dropdown-item>
    </div>
    <div class="pt-1" role="none">
        <x-logout-form role="none">
            <x-dropdown-item type="submit">
                Sign out
            </x-dropdown-item>
        </x-logout-form>
    </div>
</div>
