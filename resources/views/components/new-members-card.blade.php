<section aria-labelledby="new-members-title" {{ $attributes }}>
    <x-card>
        <h2 class="text-base font-medium text-gray-900" id="new-members-title">Nouveaux membres</h2>

        <div class="flow-root mt-6">
            <x-stacked-list>
                @foreach($newUsers as $newUser)
                    <x-avatar-group :user="$newUser">
                        <x-slot name="title">{{ $newUser->name }}</x-slot>
                        <x-slot name="subtitle">{{ '@' . $newUser->username }}</x-slot>

                        <x-slot name="actions">
                            <a
                                href="{{ route('profiles.show', $newUser) }}"
                                class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Voir
                            </a>
                        </x-slot>
                    </x-avatar-group>
                @endforeach
            </x-stacked-list>
        </div>
    </x-card>
</section>
