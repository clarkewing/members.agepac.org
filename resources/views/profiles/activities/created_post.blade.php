@component('profiles.activities.activity')
    @slot('heading')
        {{ $profileUser->name }} a répondu à <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a>
    @endslot

    @slot('body')
        {{ $activity->subject->body }}
    @endslot
@endcomponent
