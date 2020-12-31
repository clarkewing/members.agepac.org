@extends('layouts.app')

@push('styles')
<style type="text/css">
    #overflowContainer::before {
        content: '';
        width: 2rem;
        height: calc(100% + 2px);
        position: absolute;
        right: 0;
        top: -1px;
        background: linear-gradient(to left, rgba(255,255,255,1) 0%, rgba(255,255,255,0.5) 45%, rgba(255,255,255,0) 100%);
        z-index: 1;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="container bg-white shadow-sm" style="margin-top: -1.5rem;">
    <div class="row justify-content-center py-4">
        <div class="col-md-12">
            <h3>Quoi de neuf ?</h3>

            <div id="overflowContainer" class="position-relative mb-3">
                <div class="d-flex flex-row flex-nowrap mx-0" style="overflow-x: scroll; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;">

                    @foreach($threadUpdates as $thread)
                        <a href="{{ $thread->path() }}" class="d-inline-flex ml-0 mr-3" style="scroll-snap-align: start; flex: 0 0 auto;">
                            <div class="card bg-dark text-white w-full" style="width: 300px;">
                                <div class="card-body pb-0">
                                    <h5 class="card-title text-truncate">{{ $thread->title }}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <p class="card-text small">
                                        @if($thread->replies_count > 0)
                                            @php $latestReply = $thread->replies()->latest()->first() @endphp
                                            {{ $latestReply->owner->name }} a répondu il y a {{ $latestReply->created_at->diffForHumans() }}
                                        @else
                                            Ouvert par {{ $thread->creator->name }} {{ $thread->created_at->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <h3>À la une</h3>
            <section>
                <h4 class="text-primary">
                    <a href="{{ $latestAnnouncement->path() }}">
                        {{ $latestAnnouncement->title }}
                    </a>
                </h4>
                <div>{!! $latestAnnouncement->initiatorPost->body !!}</div>
                </section>
        </div>
        <div class="col-md-4">
            <h3>Activité</h3>
            <table class="table">
                <tbody>
                    @foreach($feed as $activity)
                        <tr><td>
                            <p class="mb-0">
                                @switch($activity->type)
                                    @case('created_user')
                                        <a href="{{ route('profiles.show', $activity->subject) }}">{{ $activity->subject->name }}</a>
                                        a rejoint l'AGEPAC !
                                        @break
                                    @case('updated_profile')
                                        <a href="{{ route('profiles.show', $activity->subject) }}">{{ $activity->subject->name }}</a>
                                        a mis à jour son profil.
                                        @break
                                    @case('created_thread')
                                        <a href="{{ route('profiles.show', $activity->subject->creator) }}">{{ $activity->subject->creator->name }}</a>
                                        a publié
                                        <a href="{{ $activity->subject->path() }}">{{ $activity->subject->title }}</a>
                                        @break
                                    @case('created_post')
                                        <a href="{{ route('profiles.show', $activity->subject->owner) }}">{{ $activity->subject->owner->name }}</a>
                                        a répondu à
                                        <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a>
                                        @break
                                @endswitch
                            </p>
                            <p class="small text-muted text-right mb-0">{{ $activity->created_at->diffForHumans() }}</p>
                        </td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
