<x-linklist title="Sujets chauds">
    <ul class="list-unstyled">
        @foreach($trending as $thread)
            <li>
                <a href="{{ $thread->path }}">
                    {{ $thread->title }}
                </a>
            </li>
        @endforeach
    </ul>
</x-linklist>
