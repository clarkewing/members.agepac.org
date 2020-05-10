<a href="{{ route('threads.create', ['channel_id' => optional(Route::input('channel'))->id]) }}"
   class="btn btn-block btn-success mb-3">
    Nouvelle discussion
</a>

@include('threads._search-bar')

@include('threads._nav-browse')

@if(! Route::is('threads.search') && count($trending))
    @include('threads._nav-trending')
@endif

@include('threads._nav-channels')
