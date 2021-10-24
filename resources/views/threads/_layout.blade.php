@extends('layouts.legacy')

@section('content')
    @if(Route::is('threads.search'))
        <instant-search inline-template>
            <ais-instant-search
                :search-client="searchClient"
                index-name="{{ (new App\Models\Post)->searchableAs() }}"
                :routing="routing"
            >
                <ais-configure query="{{ Request::query('query') }}"></ais-configure>
                @endif

                <div class="container-lg border-bottom border-lg-x rounded-bottom my-n4">
                    <div class="row flex-sm-nowrap">
                        @section('sidebar')
                            @include('threads._sidebar')
                        @show

                        <div class="col py-4 px-sm-3 px-md-5 bg-white">
                            @section('breadcrumbs')
                                @include('threads._breadcrumbs')
                            @show

                            @yield('main')
                        </div>
                    </div>
                </div>

                @if(Route::is('threads.search'))
            </ais-instant-search>
        </instant-search>
    @endif
@endsection
