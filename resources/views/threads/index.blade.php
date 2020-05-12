@extends('threads._layout')

@section('main')
    @forelse($threads as $thread)
        <thread-result :data="{{ json_encode($thread) }}"></thread-result>

    @empty
        @include('threads._no-results')
    @endforelse

    <div class="d-flex justify-content-center">
        {{ $threads->links() }}
    </div>
@endsection
