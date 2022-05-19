@props(['user', 'imgClass' => 'h-8 w-8'])

<li {{ $attributes->except('href')->class([
    'py-4' => ! Str::contains($attributes->get('class'), 'py-'),
]) }}>
    <div class="flex items-center space-x-4">
        <div class="shrink-0">
            <img
                class="{{ $imgClass }} rounded-full"
                src="{{ $user->getProfilePhotoUrlAttribute() }}"
                alt="{{ $user->name }}"
            />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">{{ $title }}</p>
            <p class="text-sm text-gray-500 truncate">{{ $subtitle }}</p>
        </div>
        <div>
            {{ $actions }}
        </div>
    </div>
</li>
