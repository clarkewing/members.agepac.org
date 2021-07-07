@extends('layouts.app')

@section('content')
    <div class="container text-primary bg-white mt-n4 py-4 border border-top-0 rounded-bottom">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <h1 class="text-center mb-5">{{ $title }}</h1>

                <article>{!! $body !!}</article>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ mix('vendor/laraberg/css/laraberg.css') }}" rel="stylesheet">
@endpush
