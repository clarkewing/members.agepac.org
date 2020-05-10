@extends('threads._layout')

@section('main')
    <thread-view :thread="{{ $thread }}" inline-template>
        <div>
            @include('threads._question')

            <posts @added="postsCount++" @removed="postsCount--"></posts>
        </div>
    </thread-view>
@endsection
