@extends('threads._layout')

@section('main')
    <livewire:thread-header :thread="$thread" />

    <livewire:thread-poll :thread="$thread" />
    @if(Auth::user()->can('attachPoll', $thread) || (! is_null($thread->poll) && Auth::user()->can('update', $thread->poll)))
        <livewire:thread-poll-form :thread="$thread" />
    @endif

    <livewire:thread-posts :thread="$thread" />

    <livewire:thread-new-post :thread="$thread" />

    {{--    <thread-view :thread="{{ $thread }}" inline-template>--}}
{{--        <div>--}}
{{--            @include('threads._title-header')--}}

{{--            <thread-poll ref="poll" :initial-poll="thread.poll"></thread-poll>--}}

{{--            <posts @added="repliesCount++" @removed="repliesCount--"></posts>--}}
{{--        </div>--}}
{{--    </thread-view>--}}
@endsection
