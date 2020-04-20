@component('profiles.activities.activity')
    @slot('heading')
        {{ $profileUser->name }} favorited <a href="{{ $activity->subject->favoritable->path() }}">a reply</a>.
    @endslot

    @slot('body')
        {{ $activity->subject->favoritable->body }}
    @endslot
@endcomponent
