@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <avatar-form :user="{{ $profileUser }}"></avatar-form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            @forelse($activities as $date => $activity)
                <h2 class="pb-2 border-bottom mb-4">{{ $date }}</h2>
                @foreach($activity as $record)
                    @if(View::exists("profiles.activities.{$record->type}"))
                        @include("profiles.activities.{$record->type}", ['activity' => $record])
                    @endif
                @endforeach
            @empty
                <p>Cet utilisateur n'a pas encore d'activité.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
