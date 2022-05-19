<div {{ $attributes }}>
    <h1 class="sr-only">Fil d’actualités</h1>

    <ul role="list" class="space-y-4">
        @foreach(\App\Models\Thread::latest()->take(5)->get()->map(fn($thread) => $thread->posts()->latest()->first()) as $post)
            <li>
                <x-post-card :post="$post"/>
            </li>
        @endforeach
    </ul>
</div>
