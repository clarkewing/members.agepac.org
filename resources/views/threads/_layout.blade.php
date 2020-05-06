@extends('layouts.app')

@section('content')
    <div class="container border-left border-right mt-n4">
        <div class="row">
            <div class="col-md-3 p-md-4 bg-light border-right">
                @section('sidebar')
                    @include('threads._sidebar')
                @show
            </div>

            <div class="col-md-9 px-md-5 py-4 bg-white">
                @yield('main')
            </div>
        </div>
    </div>
@endsection
