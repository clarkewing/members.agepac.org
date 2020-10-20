@extends('layouts.form-with-bg')

@section('card-body')
    <poll-results channelslug="{{ $channelSlug }}" :thread="{{ $thread }}" :poll="{{ $poll }}"></poll-results>
@endsection
