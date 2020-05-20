<div id="forumSidebar" class="col-sm-4 d-flex flex-row-reverse flex-sm-column py-3 py-sm-4 px-sm-3 px-md-4 bg-light border-sm-right">

    @include('threads._create-btn')

    @include('threads._search-bar')

    @include('threads._nav-browse')

    @if(! Route::is('threads.search') && count($trending))
        @include('threads._nav-trending')
    @endif

    @include('threads._nav-channels')

    @include('threads._nav-dropdown')

</div>
