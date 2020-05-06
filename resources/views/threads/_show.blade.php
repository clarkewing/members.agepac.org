<thread-view :thread="{{ $thread }}" inline-template>
    <div>
        @include('threads._question')

        <replies @added="repliesCount++" @removed="repliesCount--"></replies>
    </div>
</thread-view>
