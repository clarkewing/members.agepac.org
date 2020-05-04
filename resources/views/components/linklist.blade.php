@props(['title', 'spacing' => 1])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>

    <h4 class="h6 text-uppercase font-weight-bold text-gray-500">{{ $title }}</h4>

    <ul class="list-unstyled spacing-{{ $spacing }}">
        {{ $slot }}
    </ul>
</div>
