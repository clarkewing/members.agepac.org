@props(['label' => null, 'name', 'value', 'help' => null])

<x-form._form-group
    :label="$label"
    :name="$name"
    :help="$help"
>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value ?? '') }}"
        {{ $attributes
            ->class([
                'appearance-none block w-full px-3 py-2 border rounded-md focus:outline-none sm:text-sm',
                'border-gray-300 placeholder-gray-400 focus:ring-wedgewood-500 focus:border-wedgewood-500' => ! $errors->has($name),
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
    />
</x-form._form-group>
