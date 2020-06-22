@component('profiles.activities.activity')
    @slot('heading')
        {{ $profileUser->name }} a aimé <a href="{{ $activity->subject->favoritable->path() }}">un post</a>.
    @endslot

    @slot('body')
        {{ $activity->subject->favoritable->body }}
    @endslot
@endcomponent
