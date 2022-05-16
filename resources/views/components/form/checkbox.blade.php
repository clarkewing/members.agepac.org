@props(['label', 'name', 'checked' => false])

<div class="flex items-center">
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="checkbox"
        {{ $attributes->merge([
            'class' => 'h-4 w-4 text-wedgewood-600 focus:ring-wedgewood-500 border-gray-300 rounded',
        ]) }}
        @checked(old($name, $checked))
    >

    <label
        for="{{ $name }}"
        class="ml-2 block text-sm text-gray-900"
    >
        {{ $label }}
    </label>
</div>
