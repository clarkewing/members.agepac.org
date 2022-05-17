@props(['label', 'name', 'options', 'value', 'help' => null])

@php
    if (! Arr::isAssoc($options)) {
        $options = array_combine($options, $options);
    }
@endphp

<x-form._form-group
    :label="$label"
    :name="$name"
    :help="$help"
>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes
            ->class([
                'appearance-none block w-full pl-3 py-2 border rounded-md focus:outline-none sm:text-sm',
                'pr-10 border-gray-300 placeholder-gray-400 focus:ring-wedgewood-500 focus:border-wedgewood-500' => ! $errors->has($name),
                'pr-10 border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' => $errors->has($name),
            ])
            ->merge([
                'type' => 'text',
            ])
        }}
        wire:loading.attr="disabled"
        @error($name)
        aria-invalid="true"
        aria-describedby="{{ $name }}-error"
        @elseif($help)
        aria-describedby="{{ $name }}-description"
        @enderror
    >
        <option value="" disabled></option>
        @foreach($options as $optionValue => $optionName)
            <option
                value="{{ $optionValue }}"
                @selected(old($name, $value) == $optionValue)
            >
                {{ $optionName }}
            </option>
        @endforeach
    </select>

    <x-slot name="error-icon">
        {{-- TODO: Figure out how to shift dropdown arrow to fit exclamation circle --}}
{{--        @error($name)--}}
{{--            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">--}}
{{--                <x-heroicon-s-exclamation-circle class="h-5 w-5 text-red-500" aria-hidden="true" />--}}
{{--            </div>--}}
{{--        @enderror--}}
    </x-slot>
</x-form._form-group>
