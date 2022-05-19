@props(['href'])

<div class="px-5 py-1">
    <a
        href="{{ $href }}"
        class="text-sm text-gray-500 hover:text-gray-900"
    >
        {{ $slot }}
    </a>
</div>
