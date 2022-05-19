<div {{ $attributes->class(['max-w-lg mx-auto px-4']) }}>
    <div class="text-center">
        <x-heroicon-o-inbox class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true"/>
        <h2 class="mt-2 text-lg font-medium text-gray-900">Tu ne suis rien</h2>
        <p class="mt-1 text-sm text-gray-500">
            Tu n’as pas encore suivi de discussions ou de membres. Avec le suivi, tu seras notifié
            lorsqu’une discussion reçoit une réponse ou qu’un membre met à jour son profil.
        </p>
    </div>

    <div class="mt-10">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
            Membres fréquemment suivis
        </h3>
        <x-stacked-list class="mt-4 my-0 border-t border-b border-gray-200">
            @foreach(\App\Models\User::inRandomOrder()->take(3)->get() as $user)
                <x-avatar-group :user="$user">
                    <x-slot name="title">{{ $user->name }}</x-slot>
                    <x-slot name="subtitle">{{ $user->course }}</x-slot>

                    <x-slot name="actions">
                        <button
                            type="button"
                            class="inline-flex items-center py-2 px-3 border border-transparent rounded-full bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <x-heroicon-s-plus-sm class="-ml-1 mr-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                            <span class="text-sm font-medium text-gray-900">
                                Suivre <span class="sr-only">{{ $user->name }}</span>
                            </span>
                        </button>
                    </x-slot>
                </x-avatar-group>
            @endforeach
        </x-stacked-list>
    </div>
</div>
