@extends('layouts.app')

@section('content')
    @if(Route::is('threads.search'))
        <instant-search inline-template>
            <ais-instant-search :search-client="searchClient" index-name="posts" :routing="routing">
                <ais-configure query="{{ Request::query('query') }}"></ais-configure>
                @endif

                <div class="container-lg border-bottom border-lg-x rounded-bottom my-n4">
                    <div class="row">
                        <div class="col-sm-4 py-4 px-sm-3 px-md-4 bg-light border-right"
                             style="min-width: 200px; max-width: 280px;">
                            @section('sidebar')
                                @include('threads._sidebar')
                            @show
                        </div>

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
