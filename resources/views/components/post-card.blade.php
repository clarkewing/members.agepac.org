@props(['post'])

<x-card tag="article" aria-labelledby="post-title-{{ $post->id }}">
    <div class="flex space-x-3">
        <div class="flex-shrink-0">
            <img
                class="h-10 w-10 rounded-full"
                src="{{ $post->owner->getProfilePhotoUrlAttribute() }}"
                alt="{{ $post->owner->name }}"
            />
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-900">
                <a href="{{ route('profiles.show', $post->owner) }}" class="hover:underline">
                    {{ $post->owner->name }}
                </a>
            </p>
            <p class="text-sm text-gray-500">
                <time datetime="{{ $post->created_at->toAtomString() }}">
                    {{ $post->created_at->isoFormat('LLL') }}
                </time>
            </p>
        </div>
        <div class="flex-shrink-0 self-center flex">
            <x-dropdown origin="right">
                <x-slot name="button">
                    <div class="-m-2 p-2 rounded-full flex items-center text-gray-400 hover:text-gray-600">
                        <span class="sr-only">Open options</span>
                        <x-heroicon-s-dots-vertical class="h-5 w-5" aria-hidden="true"/>
                    </div>
                </x-slot>

                <x-dropdown-item>
                    <x-heroicon-s-star class="mr-3 h-5 w-5 text-gray-400" aria-hidden="true"/>
                    <span>Suivre</span>
                </x-dropdown-item>

                <x-dropdown-item>
                    <x-heroicon-s-flag class="mr-3 h-5 w-5 text-gray-400" aria-hidden="true"/>
                    <span>Signaler</span>
                </x-dropdown-item>
            </x-dropdown>
        </div>
    </div>

    <div class="relative">
        <a href="{{ $post->path() }}" class="hover:underline focus:outline-none">
            <span class="absolute inset-0" aria-hidden="true"></span>
            <h3 id="post-title-{{ $post->id }}" class="mt-4 text-base font-medium text-gray-900">
                {{ $post->thread->title }}
            </h3>
        </a>

        <div class="mt-2 text-sm text-gray-700 space-y-4 line-clamp-5">
            {!! $post->body !!}
        </div>
    </div>

    <div class="mt-6 flex justify-between space-x-8">
        <div class="flex space-x-6">
            <div class="inline-flex space-x-2 text-sm text-gray-400">
                <x-heroicon-s-thumb-up class="h-5 w-5" aria-hidden="true"/>
                <span class="font-medium text-gray-900">{{ $post->favorites_count }}</span>
                <span class="sr-only">likes</span>
            </div>
            <div class="inline-flex space-x-2 text-sm text-gray-400">
                <x-heroicon-s-chat-alt class="h-5 w-5" aria-hidden="true"/>
                <span class="font-medium text-gray-900">{{ $post->thread->replies_count }}</span>
                <span class="sr-only">replies</span>
            </div>
            <div class="inline-flex space-x-2 text-sm text-gray-400">
                <x-heroicon-s-eye class="h-5 w-5" aria-hidden="true"/>
                <span class="font-medium text-gray-900">{{ $post->thread->visits }}</span>
                <span class="sr-only">views</span>
            </div>
        </div>
        <div class="flex text-sm">
            <button type="button" class="inline-flex space-x-2 text-sm text-gray-400 hover:text-gray-500">
                <x-heroicon-s-reply class="h-5 w-5" aria-hidden="true"/>
                <span class="font-medium text-gray-900">Répondre</span>
            </button>
        </div>
    </div>
</x-card>
