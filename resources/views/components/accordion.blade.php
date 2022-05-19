@props(['active' => null])

<div
    x-data="{ active: @js($active) }"
    {{ $attributes }}
>
    {{ $slot }}
</div>
