@forelse($threads as $thread)
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center">
            <div class="flex-grow-1">
                <h5>
                    @if($thread->pinned)
                        <svg class="d-inline bi-bookmark-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 3a2 2 0 012-2h6a2 2 0 012 2v12l-5-3-5 3V3z" clip-rule="evenodd"/>
                        </svg>
                    @endif

                    <a href="{{ $thread->path() }}">
                        @if(Auth::check() && $thread->hasUpdatesFor(Auth::user()))
                            <strong>{{ $thread->title }}</strong>
                        @else
                            <span class="text-muted">{{ $thread->title }}</span>
                        @endif
                    </a>
                </h5>
                <h6 class="mb-0">
                    Publié par <a href="{{ route('profiles.show', $thread->creator) }}">{{ $thread->creator->name }}</a>
                </h6>
            </div>

            <a href="{{ $thread->path() }}">{{ $thread->replies_count }} {{ Str::plural('réponse', $thread->replies_count) }}</a>
        </div>

        <div class="card-body">
            <div class="body">{!! $thread->body !!}</div>
        </div>

        <div class="card-footer text-muted d-flex align-items-center justify-content-between">
            <div>
                {{ $thread->visits()->count() }} vues
            </div>

            <a href="{{ route('threads.index', $thread->channel) }}"
               class="badge badge-pill badge-info font-size-normal font-weight-normal"
            >
                {{ $thread->channel->name }}
            </a>
        </div>
    </div>
@empty
    <p>Il n'y a aucune discussion pertinente.</p>
@endforelse
