@props(['options' => []])

@php
    $options = array_merge(
        [
            'alpha' => false,
            'color' => $attributes->get('value', '#FFFFFF'),
        ],
        $options
    );
@endphp

<div
    x-data="{ color: '{{ $options['color'] }}' }"
    x-init="
        picker = new Picker($refs.button);
        picker.setOptions({{ json_encode((object) $options) }});
        picker.onDone = rawColor => {
            color = rawColor.hex.substring(0, 7);
            $dispatch('input', color)
        }
    "
    wire:ignore
    {{ $attributes }}
>
    <button
        class="form-control px-3"
        x-ref="button"
        x-bind:style="`background: ${color}`"
    ></button>
</div>

@once
    @push('scripts')
        <script src="https://unpkg.com/vanilla-picker@2"></script>
    @endpush
@endonce
