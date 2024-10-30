@component('profiles.activities.activity')
    @slot('heading')
        {{ $profile->name }} a aimé
        @if(is_null($activity->subject))
            un post qui n'existe plus.
        @else
            <a href="{{ $activity->subject->favoritable->path() }}">un post</a>.
        @endif
    @endslot

    @unless(is_null($activity->subject))
        @slot('body')
            {{ $activity->subject->favoritable->body }}
        @endslot
    @endunless
@endcomponent
