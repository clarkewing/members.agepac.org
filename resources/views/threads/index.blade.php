@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @include('threads._list')

            {{ $threads->render() }}
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Recherche</div>

                <div class="card-body">
                    <form method="get" action="{{ route('threads.search') }}">
                        <div class="form-group">
                            <input type="text"
                                   placeholder="Chercher une discussion..."
                                   name="q"
                                   class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </form>
                </div>
            </div>

            @if(count($trending))
                <div class="card mb-4">
                    <div class="card-header">Sujets chauds</div>

                    <div class="list-group list-group-flush">
                        @foreach($trending as $thread)
                            <a href="{{ $thread->path }}" class="list-group-item list-group-item-action">{{ $thread->title }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
