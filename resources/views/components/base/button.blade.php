@props(['href', 'type' => 'button'])

@isset($href)
    <a
        href="{{ $href }}"
        {{ $attributes }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes }}
    >
        {{ $slot }}
    </button>
@endisset
