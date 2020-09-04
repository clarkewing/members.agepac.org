@extends('layouts.form-with-bg')

@section('card-body')
<poll-vote channelslug="{{ $channelSlug }}" :thread="{{ $thread }}" :poll="{{ $poll }}"></poll-vote>
@endsection