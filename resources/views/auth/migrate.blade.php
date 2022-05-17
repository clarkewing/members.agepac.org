@extends('layouts.form-with-bg')

@push('scripts')
    @livewireScripts
@endpush

@section('card-body')
    <livewire:migrate />
@endsection
