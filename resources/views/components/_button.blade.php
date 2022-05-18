@if($attributes->has('href'))
    <a {{ $attributes }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button']) }}>
        {{ $slot }}
    </button>
@endif
