<x-base.button
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500']) }}
>
    {{ $slot }}
</x-base.button>
