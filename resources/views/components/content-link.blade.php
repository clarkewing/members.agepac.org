@props(['href'])

<li {{ $attributes->class([
    'py-5' => ! Str::contains($attributes->get('class'), 'py-'),
]) }}>
    <div class="relative focus-within:ring-2 focus-within:ring-wedgewood-500">
        <h3 class="text-sm font-semibold text-gray-800">
            <a
                href="{{ $href }}"
                class="hover:underline focus:outline-none"
            >
                <span class="absolute inset-0" aria-hidden="true"></span>
                {{ $title }}
            </a>
        </h3>

        <p class="mt-1 text-sm text-gray-600 line-clamp-2">
            {{ $slot }}
        </p>
    </div>
</li>
