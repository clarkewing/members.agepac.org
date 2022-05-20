@props(['size' => 'base', 'round' => false])

<x-base.button
    {{ $attributes->class([
        'w-full flex items-center justify-center border border-transparent shadow-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500',
        'px-2.5 py-0.5 text-sm' => $size === 'xs',
        'px-4 py-2 text-sm' => $size === 'base',
        'rounded-md' => ! $round,
        'rounded-full' => $round,
    ]) }}
>
    {{ $slot }}
</x-base.button>
