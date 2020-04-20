@extends('layouts.app')

@section('content')
    <div class="container">
        <instant-search inline-template>
            <ais-instant-search :search-client="searchClient" index-name="threads">

                <ais-configure query="{{ Request::query('q') }}"></ais-configure>

                <div class="row">
                    <div class="col-md-8">
                        <ais-hits>
                            <div slot="item" slot-scope="{ item }">
                                <a :href="item.path">
                                    <ais-highlight :hit="item" attribute="title"></ais-highlight>
                                </a>
                            </div>
                        </ais-hits>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">Recherche</div>

                            <div class="card-body">
                                <ais-search-box placeholder="Rechercher quelque chose..."
                                                autofocus
                                                :class-names="{
                                                    'ais-SearchBox-input': 'form-control',
                                                    'ais-SearchBox-submit': 'd-none',
                                                    'ais-SearchBox-reset': 'd-none'
                                                }">
                                </ais-search-box>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">Filtrer par Canal</div>

                            <div class="card-body">
                                <ais-refinement-list attribute="channel.name"></ais-refinement-list>
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
            </ais-instant-search>
        </instant-search>
    </div>
@endsection
