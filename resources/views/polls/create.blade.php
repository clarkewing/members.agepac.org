@extends('layouts.form-with-bg')

@section('card-body')
    <poll-form :thread="{{ $thread }}"></poll-form>
@endsection
