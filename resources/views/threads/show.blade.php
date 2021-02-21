@extends('threads._layout')

@section('main')
    <livewire:thread :thread="$thread" />

{{--    <thread-view :thread="{{ $thread }}" inline-template>--}}
{{--        <div>--}}
{{--            @include('threads._title-header')--}}

{{--            <thread-poll ref="poll" :initial-poll="thread.poll"></thread-poll>--}}

{{--            <posts @added="repliesCount++" @removed="repliesCount--"></posts>--}}
{{--        </div>--}}
{{--    </thread-view>--}}
@endsection
