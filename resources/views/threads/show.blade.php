@extends('threads._layout')

@section('main')
    <thread-view :thread="{{ json_encode($thread) }}" inline-template>
        <div>
            @include('threads._title-header')

            <thread-poll ref="poll" :initial-poll="thread.poll"></thread-poll>

            <posts @added="repliesCount++" @removed="repliesCount--"></posts>
        </div>
    </thread-view>
@endsection
