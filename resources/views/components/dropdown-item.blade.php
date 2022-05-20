<x-base.button
    {{ $attributes
        ->class([
            'flex w-full px-4 py-2 text-gray-700 text-left text-sm truncate hover:text-gray-900 hover:bg-gray-100',
        ])
        ->merge(['role' => 'menuitem'])
    }}
>
    {{ $slot }}
</x-base.button>
