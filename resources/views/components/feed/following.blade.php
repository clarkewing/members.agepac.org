<div {{ $attributes }}>
    <h1 class="sr-only">Fil d’entités suivies</h1>

    <ul role="list" class="space-y-4">
        @forelse([] as $post)
            <li>
                <x-post-card :post="$post"/>
            </li>
        @empty
            <x-feed.following.empty class="mt-8" />
        @endforelse
    </ul>
</div>
