@props(['label', 'name', 'help' => null])

<div class="space-y-1">
    @isset($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endisset

    <div class="space-y-2">
        <div class="relative rounded-md shadow-sm">
            {{ $slot }}

            @error($name)
                @isset($errorIcon)
                    {{ $errorIcon }}
                @else
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <x-heroicon-s-exclamation-circle class="h-5 w-5 text-red-500" aria-hidden="true" />
                    </div>
                @endisset
            @enderror
        </div>

        @error($name)
            <p
                class="text-sm text-red-600"
                id="{{ $name }}-error"
            >
                {{ $message }}
            </p>
        @elseif($help)
            <p
                class="text-sm text-gray-500"
                id="{{ $name }}-description"
            >
                {{ $help }}
            </p>
        @enderror
    </div>
</div>
