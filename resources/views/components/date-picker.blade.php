@props(['options' => []])

@php
    $options = array_merge(
        [
            'dateFormat' => ($options['enableTime'] ?? false) ? 'Y-m-d H:i:S' : 'Y-m-d',
            'enableTime' => false,
            'time_24hr' => true,
            'altFormat' =>  ($options['enableTime'] ?? false) ? 'j F Y H:i' : 'j F Y',
            'altInput' => true
        ],
        $options
    );
@endphp

<div wire:ignore>
    <input
        x-data
        x-init="flatpickr($refs.input, {{ json_encode((object) $options) }})"
        x-ref="input"
        type="text"
        {{ $attributes->merge(['class' => 'form-control']) }}
    />
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @endpush
@endonce
