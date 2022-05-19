@props(['active' => false])

@php
    $tag = $attributes->has('href') ? 'a' : 'button';
@endphp

<{{ $tag }}
    {{ $attributes
        ->class([
            'text-gray-500 hover:text-gray-700 first:rounded-l-lg last:rounded-r-lg group relative min-w-0 flex-1 overflow-hidden bg-white py-3 px-2 sm:py-4 sm:px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10',
            'text-gray-900' => $active,
            'text-gray-500 hover:text-gray-700' => ! $active,
        ])
        ->merge($tag === 'a' ? ['href' => '#'] : ['type' => 'button'])
    }}
    @if($active) aria-current="page" @endif
>
    <span>{{ $slot }}</span>
    <span aria-hidden="true" class="{{ $active ? 'bg-wedgewood-500' : 'bg-transparent' }} absolute inset-x-0 bottom-0 h-0.5"></span>
</{{ $tag }}>
