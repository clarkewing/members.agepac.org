@extends('layouts.app')

@push('styles')
<style type="text/css">
    body {
        height: 100%;
    }
</style>

@section('body')
<div class="d-flex flex-column" style="min-height: 100%;">
    @include('layouts.navbar')

    <div class="d-flex flex-column flex-grow-1" style="background: url({{ asset('background.jpg') }}) 50% 50%; background-size: cover;">
        <div class="container-fluid d-flex flex-column flex-grow-1" id="app">
            <div class="d-flex flex-grow-1 justify-content-center align-items-center">
                <div class="my-3 my-md-4" style="width: 400px; max-width: 100%;">
                    <div class="card">
                        <div class="card-body">
                            @yield('card-body')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p class="small text-white text-center m-2">Photo de Marek Madl - C. ATPL 2015</p>
    </div>
</div>
@include('layouts.footer')
@endsection
