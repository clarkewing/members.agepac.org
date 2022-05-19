@props(['icon', 'active' => false, 'child' => false])

@once
    @push('styles')
        <style>
            .collapse-caret {
                display: none;
            }

            [x-data="collapse"] > [aria-controls] > .group > .collapse-caret {
                display: block;
            }
        </style>
    @endpush
@endonce

@php
    $tag = $attributes->has('href') ? 'a' : 'button';
@endphp

<{{ $tag }}
    {{ $attributes
        ->class([
            'group w-full flex items-center px-3 py-2 text-sm font-medium rounded-md',
            'pl-11' => $child,
            'bg-gray-200 text-gray-900' => $active,
            'text-gray-600 hover:text-gray-900 hover:bg-gray-50' => ! $active,
        ])
        ->merge($tag === 'a' ? ['href' => '#'] : ['type' => 'button'])
    }}
    @if($active) aria-current="page" @endif
>
    @isset($icon)
        <x-dynamic-component
            :component="$icon"
            class="shrink-0 -ml-1 mr-3 h-6 w-6 {{ $active ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500' }}"
            aria-hidden="true"
        />
    @endif

    <span class="flex-1 text-left truncate">
          {{ $slot }}
    </span>

    <svg
        viewBox="0 0 20 20"
        class="collapse-caret ml-3 shrink-0 h-5 w-5 group-hover:text-gray-400 transition ease-in-out duration-150"
        :class="((typeof expanded === 'undefined') ? false : expanded) ? 'text-gray-400 rotate-90' : 'text-gray-300'"
        aria-hidden="true"
    >
        <path d="M6 6L14 10L6 14V6Z" fill="currentColor" />
    </svg>
</{{ $tag }}>
