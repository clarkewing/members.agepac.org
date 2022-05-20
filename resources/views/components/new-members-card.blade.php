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
                            <x-button.white size="xs" round href="{{ route('profiles.show', $newUser) }}">
                                Voir
                            </x-button.white>
                        </x-slot>
                    </x-avatar-group>
                @endforeach
            </x-stacked-list>
        </div>
    </x-card>
</section>
