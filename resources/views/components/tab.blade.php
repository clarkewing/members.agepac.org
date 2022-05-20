@props(['active' => false])

<x-base.button
    {{ $attributes
        ->class([
            'group relative min-w-0 flex-1 py-3 px-2 sm:py-4 sm:px-4 text-sm text-center font-medium bg-white first:rounded-l-lg last:rounded-r-lg overflow-hidden hover:bg-gray-50 focus:z-10',
            'text-gray-900' => $active,
            'text-gray-500 hover:text-gray-700' => ! $active,
        ])
        ->when($active, fn($attributes) => $attributes->merge(['aria-current' => 'page']))
    }}
>
    <span>{{ $slot }}</span>
    <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-0.5 {{ $active ? 'bg-wedgewood-500' : 'bg-transparent' }}"></span>
</x-base.button>
