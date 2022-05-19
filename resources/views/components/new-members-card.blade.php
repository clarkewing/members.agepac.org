<section aria-labelledby="new-members-title" {{ $attributes }}>
    <x-card>
        <h2 class="text-base font-medium text-gray-900" id="new-members-title">Nouveaux membres</h2>

        <div class="flow-root mt-6">
            <x-stacked-list>
                @foreach(\App\Models\User::latest()->take(4)->get() as $user)
                    <x-avatar-group :user="$user"/>
                @endforeach
            </x-stacked-list>
        </div>
    </x-card>
</section>
