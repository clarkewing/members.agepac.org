@props(['submit'])

<form wire:submit.prevent="{{ $submit }}" {{ $attributes }}>
    <div class="shadow sm:overflow-hidden sm:rounded-md">
        <div class="space-y-6 bg-white py-6 px-4 sm:p-6">
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $title }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
            </div>

            {{ $form }}
        </div>

        @if (isset($actions))
            <div class="flex items-center justify-end bg-gray-50 px-4 py-3 text-right sm:px-6">
                {{ $actions }}
            </div>
        @endif
    </div>
</form>
