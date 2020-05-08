<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 pt-0">
        <li class="breadcrumb-item">
            @if (Route::is('threads.index') && ! Route::input('channel') && empty(Request::query()))
                Forum
            @else
                <a href="{{ route('threads.index') }}">
                    Forum
                </a>
            @endif
        </li>

        @if (Route::is('threads.index') && Route::input('channel'))
            <li class="breadcrumb-item">
                {{ ucwords($channel->name) }}
            </li>
        @endif

        @if (Route::is('threads.index') && Request::has('by'))
            <li class="breadcrumb-item">
                @if(Auth::user()->username === Request::query('by'))
                    Mes discussions
                @else
                    Discussions de {{ $threadsUser->name }}
                @endif
            </li>
        @endif

        @if (Route::is('threads.index') && Request::has('popular'))
            <li class="breadcrumb-item">
                Discussions populaires
            </li>
        @endif

        @if (Route::is('threads.index') && Request::has('unanswered'))
            <li class="breadcrumb-item">
                Discussions sans réponse
            </li>
        @endif

        @if (Route::is('threads.show'))
            <li class="breadcrumb-item">
                <a href="{{ route('threads.index', $thread->channel) }}">
                    {{ $thread->channel->name }}
                </a>
            </li>

            <li class="breadcrumb-item">
                {{ $thread->title }}
            </li>
        @endif
    </ol>
</nav>
