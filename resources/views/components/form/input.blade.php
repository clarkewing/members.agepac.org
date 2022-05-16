@props(['label', 'name', 'value'])

<div class="space-y-1">
    @isset($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endisset

    <div class="relative rounded-md shadow-sm">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value ?? '') }}"
            {{ $attributes->class([
                'appearance-none block w-full px-3 py-2 border rounded-md focus:outline-none sm:text-sm',
                'border-gray-300 placeholder-gray-400 focus:ring-wedgewood-500 focus:border-wedgewood-500' => ! $errors->has($name),
                'pr-10 border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' => $errors->has($name),
            ])->merge(['type' => 'text']) }}
            @error($name)
                aria-invalid="true"
                aria-describedby="{{ $name }}-error"
            @enderror
        />

        @error($name)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <x-heroicon-s-exclamation-circle class="h-5 w-5 text-red-500" aria-hidden="true" />
            </div>
        @enderror
    </div>

    @error($name)
        <p
            class="mt-2 text-sm text-red-600"
            id="{{ $name }}-error"
        >
            {{ $message }}
        </p>
    @enderror
</div>
