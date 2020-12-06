@extends('layouts.form-with-bg')

@push('styles')
    @livewireStyles
@endpush

@push('scripts')
    @livewireScripts
@endpush

@section('card-body')
    <livewire:register />
@endsection
