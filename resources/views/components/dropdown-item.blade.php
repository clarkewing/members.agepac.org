@php
    $tag = $attributes->has('href') ? 'a' : 'button';
@endphp

<{{ $tag }}
    {{ $attributes
        ->class([
            'flex w-full px-4 py-2 text-gray-700 text-left text-sm truncate hover:text-gray-900 hover:bg-gray-100',
        ])
        ->merge($tag === 'a' ? ['href' => '#'] : ['type' => 'button'])
        ->merge(['role' => 'menuitem'])
    }}
>
    {{ $slot }}
</{{ $tag }}>
