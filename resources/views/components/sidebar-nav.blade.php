<x-accordion {{ $attributes->class(['space-y-1']) }}>
    <x-sidebar-nav-item
        href="{{ route('dashboard') }}"
        icon="heroicon-o-home"
        :active="true"
    >
        Dashboard
    </x-sidebar-nav-item>

    <x-collapse>
        <x-slot name="button">
            <x-sidebar-nav-item
                icon="heroicon-o-library"
                role="menuitem"
            >
                Association
            </x-sidebar-nav-item>
        </x-slot>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Statuts
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Gouvernance
        </x-sidebar-nav-item>
    </x-collapse>

    <x-collapse>
        <x-slot name="button">
            <x-sidebar-nav-item
                icon="heroicon-o-user-group"
                role="menuitem"
            >
                Réseau
            </x-sidebar-nav-item>
        </x-slot>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Annuaire des EPL
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Trombinoscopes
        </x-sidebar-nav-item>
    </x-collapse>

    <x-collapse>
        <x-slot name="button">
            <x-sidebar-nav-item
                icon="heroicon-o-briefcase"
                role="menuitem"
            >
                Carrière
            </x-sidebar-nav-item>
        </x-slot>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Annuaire des Compagnies
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            LCE
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Prorogation IRME
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Refresh MCC
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            RSFI
        </x-sidebar-nav-item>
    </x-collapse>

    <x-sidebar-nav-item
        href="{{ route('dashboard') }}"
        icon="heroicon-o-calendar"
        :active="request()->routeIs('dashboard')"
    >
        Événements
    </x-sidebar-nav-item>

    <x-sidebar-nav-item
        href="{{ route('dashboard') }}"
        icon="heroicon-o-folder"
        :active="request()->routeIs('dashboard')"
    >
        Drive
    </x-sidebar-nav-item>

    <x-collapse>
        <x-slot name="button">
            <x-sidebar-nav-item
                icon="heroicon-o-lightning-bolt"
                role="menuitem"
            >
                Outils
            </x-sidebar-nav-item>
        </x-slot>

        <x-sidebar-nav-item child
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
        >
            Préparation des vols
        </x-sidebar-nav-item>
    </x-collapse>
</x-accordion>
