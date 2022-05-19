@props(['href', 'icon'])

<a
    href="{{ $href }}"
    class="text-gray-400 hover:text-gray-500"
>
    <span class="sr-only">{{ $slot }}</span>
    <x-dynamic-component
        :component="$icon"
        class="h-6 w-6"
        aria-hidden="true"
    />
</a>
