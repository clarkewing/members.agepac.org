@props(['user'])

<li {{ $attributes->except('href')->class([
    'py-4' => ! Str::contains($attributes->get('class'), 'py-'),
]) }}>
    <div class="flex items-center space-x-4">
        <div class="flex-shrink-0">
            <img
                class="h-8 w-8 rounded-full"
                src="{{ $user->getProfilePhotoUrlAttribute() }}"
                alt="{{ $user->name }}"
            />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
            <p class="text-sm text-gray-500 truncate">{{ '@' . $user->username }}</p>
        </div>
        <div>
            <a
                href="{{ route('profiles.show', $user) }}"
                class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50"
            >
                Voir
            </a>
        </div>
    </div>
</li>
