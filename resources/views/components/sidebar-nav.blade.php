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
            href="#"
        >
            Actualités
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="#"
        >
            Statuts
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="#"
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
            href="#"
        >
            Annuaire des EPL
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="#"
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
            href="#"
        >
            Annuaire des Compagnies
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="#"
        >
            LCE
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="#"
        >
            Prorogation IRME
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="#"
        >
            Refresh MCC
        </x-sidebar-nav-item>

        <x-sidebar-nav-item child
            href="#"
        >
            RSFI
        </x-sidebar-nav-item>
    </x-collapse>

    <x-sidebar-nav-item
        href="#"
        icon="heroicon-o-calendar"
    >
        Événements
    </x-sidebar-nav-item>

    <x-sidebar-nav-item
        href="#"
        icon="heroicon-o-folder"
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
            href="#"
        >
            Préparation des vols
        </x-sidebar-nav-item>
    </x-collapse>
</x-accordion>
