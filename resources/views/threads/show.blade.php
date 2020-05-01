@extends('layouts.app')

@push('styles')
<link href="/css/vendor/tribute.css" rel="stylesheet">
@endpush

@section('content')
<thread-view :thread="{{ $thread }}" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8" v-cloak>
                @include('threads._question')

                <replies @added="repliesCount++" @removed="repliesCount--"></replies>
            </div>


            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <p>
                            Cette discussion a été publiée {{ $thread->created_at->diffForHumans() }} par
                            <a href="{{ route('profiles.show', $thread->creator) }}">{{ $thread->creator->name }}</a>, et a actuellement
                            <span v-text="repliesCount"></span> {{ Str::plural('réponse', $thread->replies_count) }}.
                        </p>


                        <div class="d-flex">
                            <subscribe-button class="mr-2" :active="{{ json_encode($thread->isSubscribedTo) }}" v-if="signedIn"></subscribe-button>
                            <button class="btn btn-outline-dark"
                                    @click="toggleLock"
                                    v-if="authorize('isAdmin')"
                                    v-text="locked ? 'Dévérouiller' : 'Vérouiller'"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
@endsection
