<a href="{{ route('threads.create', ['channel_id' => optional(Route::input('channel'))->id]) }}"
   class="btn btn-block btn-success mb-3">
    Nouvelle discussion
</a>

<form method="get" action="{{ route('threads.search') }}" class="input-group mb-4">
    <input type="text"
           placeholder="Rechercher..."
           name="q"
           class="form-control">
    <div class="input-group-append">
        <button class="btn btn-secondary" type="submit">
            <svg class="bi bi-search" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                 xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M10.442 10.442a1 1 0 011.415 0l3.85 3.85a1 1 0 01-1.414 1.415l-3.85-3.85a1 1 0 010-1.415z"
                      clip-rule="evenodd"/>
                <path fill-rule="evenodd"
                      d="M6.5 12a5.5 5.5 0 100-11 5.5 5.5 0 000 11zM13 6.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"
                      clip-rule="evenodd"/>
            </svg>
        </button>
    </div>
</form>

<x-linklist title="Naviguer" spacing="2">
    <li>
        <a class="link-muted{{ Route::is('threads.index') && ! Route::input('channel') && ! Request::query() ? ' active' : '' }}"
           href="{{ route('threads.index') }}">
            <svg class="bi bi-book text-muted mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M3.214 1.072C4.813.752 6.916.71 8.354 2.146A.5.5 0 018.5 2.5v11a.5.5 0 01-.854.354c-.843-.844-2.115-1.059-3.47-.92-1.344.14-2.66.617-3.452 1.013A.5.5 0 010 13.5v-11a.5.5 0 01.276-.447L.5 2.5l-.224-.447.002-.001.004-.002.013-.006a5.017 5.017 0 01.22-.103 12.958 12.958 0 012.7-.869zM1 2.82v9.908c.846-.343 1.944-.672 3.074-.788 1.143-.118 2.387-.023 3.426.56V2.718c-1.063-.929-2.631-.956-4.09-.664A11.958 11.958 0 001 2.82z"
                      clip-rule="evenodd"/>
                <path fill-rule="evenodd"
                      d="M12.786 1.072C11.188.752 9.084.71 7.646 2.146A.5.5 0 007.5 2.5v11a.5.5 0 00.854.354c.843-.844 2.115-1.059 3.47-.92 1.344.14 2.66.617 3.452 1.013A.5.5 0 0016 13.5v-11a.5.5 0 00-.276-.447L15.5 2.5l.224-.447-.002-.001-.004-.002-.013-.006-.047-.023a12.582 12.582 0 00-.799-.34 12.96 12.96 0 00-2.073-.609zM15 2.82v9.908c-.846-.343-1.944-.672-3.074-.788-1.143-.118-2.387-.023-3.426.56V2.718c1.063-.929 2.631-.956 4.09-.664A11.956 11.956 0 0115 2.82z"
                      clip-rule="evenodd"/>
            </svg>
            Toutes les discussions
        </a>
    </li>
    @auth
        <li>
            <a class="link-muted{{ Request::query('by') ? ' active' : '' }}"
               href="{{ route('threads.index') }}?by={{ Auth::user()->username }}">
                <svg class="bi bi-person-lines-fill text-muted mr-1" width="1em" height="1em"
                     viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 100-6 3 3 0 000 6zm7 1.5a.5.5 0 01.5-.5h2a.5.5 0 010 1h-2a.5.5 0 01-.5-.5zm-2-3a.5.5 0 01.5-.5h4a.5.5 0 010 1h-4a.5.5 0 01-.5-.5zm0-3a.5.5 0 01.5-.5h4a.5.5 0 010 1h-4a.5.5 0 01-.5-.5zm2 9a.5.5 0 01.5-.5h2a.5.5 0 010 1h-2a.5.5 0 01-.5-.5z"
                          clip-rule="evenodd"/>
                </svg>
                Mes discussions
            </a>
        </li>
    @endauth
    <li>
        <a class="link-muted{{ Request::query('popular') ? ' active' : '' }}"
           href="{{ route('threads.index') }}?popular=1" class="link">
            <svg class="bi bi-star-fill text-muted mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
            </svg>
            Discussions populaires
        </a>
    </li>
    <li>
        <a class="link-muted{{ Request::query('unanswered') ? ' active' : '' }}"
           href="{{ route('threads.index') }}?unanswered=1">
            <svg class="bi bi-inbox text-muted mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M3.81 4.063A1.5 1.5 0 014.98 3.5h6.04a1.5 1.5 0 011.17.563l3.7 4.625a.5.5 0 01-.78.624l-3.7-4.624a.5.5 0 00-.39-.188H4.98a.5.5 0 00-.39.188L.89 9.312a.5.5 0 11-.78-.624l3.7-4.625z"
                      clip-rule="evenodd"/>
                <path fill-rule="evenodd"
                      d="M.125 8.67A.5.5 0 01.5 8.5H6a.5.5 0 01.5.5 1.5 1.5 0 003 0 .5.5 0 01.5-.5h5.5a.5.5 0 01.496.562l-.39 3.124a1.5 1.5 0 01-1.489 1.314H1.883a1.5 1.5 0 01-1.489-1.314l-.39-3.124a.5.5 0 01.121-.393zm.941.83l.32 2.562a.5.5 0 00.497.438h12.234a.5.5 0 00.496-.438l.32-2.562H10.45a2.5 2.5 0 01-4.9 0H1.066z"
                      clip-rule="evenodd"/>
            </svg>
            Discussions sans réponse
        </a>
    </li>
</x-linklist>

@if(count($trending))
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
@endif

<x-linklist title="Catégories" spacing="0">
    @foreach($channels->sortBy('parent')->groupBy('parent') as $parent => $channels)
        <li>
            @if($parent)
                <span class="text-muted">{{ ucwords($parent) }}</span>
                <ul class="pl-2">
            @endif
                @foreach($channels as $channel)
                    <li class="list-unstyled">
                        <a class="link-muted{{ Route::is('threads.index') && Route::input('channel') == $channel ? ' active' : '' }}"
                           href="{{ route('threads.index', $channel) }}">
                            {{ ucwords($channel->name) }}
                        </a>
                    </li>
                @endforeach
            @if($parent)
                </ul>
            @endif
        </li>
    @endforeach
</x-linklist>