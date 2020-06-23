@component('profiles.activities.activity')
    @slot('heading')
        {{ $profileUser->name }} a publié <a href="{{ $activity->subject->path() }}">{{ $activity->subject->title }}</a>
    @endslot

    @slot('body')
        {{ $activity->subject->initiatorPost->body }}
    @endslot
@endcomponent
