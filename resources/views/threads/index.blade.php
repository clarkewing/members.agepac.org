@extends('layouts.app')

@section('content')
    <div class="container border-left border-right mt-n4">
        <div class="row">
            <div class="col-md-3 p-md-4 bg-light border-right">
                @include('threads._sidebar')
            </div>

            <div class="col-md-9 px-md-5 py-4 bg-white">
                @include('threads._list')
            </div>
        </div>
    </div>
@endsection
