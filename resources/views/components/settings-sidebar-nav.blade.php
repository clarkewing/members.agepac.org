<x-accordion {{ $attributes->class(['space-y-1']) }}>
    <x-sidebar-nav-item
        href="{{ route('dashboard') }}"
        icon="heroicon-o-user-circle"
    >
        Profil
    </x-sidebar-nav-item>

    <x-sidebar-nav-item
        href="{{ route('account.info') }}"
        icon="heroicon-o-identification"
        :active="Request::is('account/info')"
    >
        Informations
    </x-sidebar-nav-item>

    <x-sidebar-nav-item
        href="{{ route('account.security') }}"
        icon="heroicon-o-shield-check"
        :active="Request::is('account/security')"
    >
        Sûreté
    </x-sidebar-nav-item>

    <x-sidebar-nav-item
        href="{{ route('dashboard') }}"
        icon="heroicon-o-cash"
    >
        Cotisation
    </x-sidebar-nav-item>
</x-accordion>
