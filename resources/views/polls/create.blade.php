@extends('layouts.form-with-bg')

@section('card-body')
<poll-form channelslug="{{ $channelSlug }}" :thread="{{ $thread }}"></poll-form>
@endsection
