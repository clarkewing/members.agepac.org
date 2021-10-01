<div>
    @foreach ($posts as $post)
        <livewire:thread.post :post="$post" wire:key="{{ $post->id }}" />
    @endforeach

    {{ $posts->links() }}
</div>
