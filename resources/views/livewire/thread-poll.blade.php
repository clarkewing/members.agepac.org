@if($poll)
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex">
                <h5 class="card-title flex-grow-1">{{ $poll->title }}</h5>

                <div class="d-flex align-items-center flex-shrink-0 align-self-start">
                    <h6 class="text-muted mb-1">Sondage</h6>

                    @canany(['update', 'delete'], $poll)
                        <div class="dropdown ml-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown">
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear-wide-connected" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8.932.727c-.243-.97-1.62-.97-1.864 0l-.071.286a.96.96 0 0 1-1.622.434l-.205-.211c-.695-.719-1.888-.03-1.613.931l.08.284a.96.96 0 0 1-1.186 1.187l-.284-.081c-.96-.275-1.65.918-.931 1.613l.211.205a.96.96 0 0 1-.434 1.622l-.286.071c-.97.243-.97 1.62 0 1.864l.286.071a.96.96 0 0 1 .434 1.622l-.211.205c-.719.695-.03 1.888.931 1.613l.284-.08a.96.96 0 0 1 1.187 1.187l-.081.283c-.275.96.918 1.65 1.613.931l.205-.211a.96.96 0 0 1 1.622.434l.071.286c.243.97 1.62.97 1.864 0l.071-.286a.96.96 0 0 1 1.622-.434l.205.211c.695.719 1.888.03 1.613-.931l-.08-.284a.96.96 0 0 1 1.187-1.187l.283.081c.96.275 1.65-.918.931-1.613l-.211-.205a.96.96 0 0 1 .434-1.622l.286-.071c.97-.243.97-1.62 0-1.864l-.286-.071a.96.96 0 0 1-.434-1.622l.211-.205c.719-.695.03-1.888-.931-1.613l-.284.08a.96.96 0 0 1-1.187-1.186l.081-.284c.275-.96-.918-1.65-1.613-.931l-.205.211a.96.96 0 0 1-1.622-.434L8.932.727zM8 12.997a4.998 4.998 0 1 0 0-9.995 4.998 4.998 0 0 0 0 9.996z"/>
                                    <path fill-rule="evenodd" d="M7.375 8L4.602 4.302l.8-.6L8.25 7.5h4.748v1H8.25L5.4 12.298l-.8-.6L7.376 8z"/>
                                </svg>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right">
                                @can('update', $poll)
                                    <button type="button" wire:click="$emitTo('thread-poll-form', 'openForm')" class="dropdown-item">
                                        Modifier le Sondage
                                    </button>
                                @endcan
                                @can('delete', $poll)
                                    <button type="button"
                                            x-data="{}"
                                            x-on:click="if(window.confirm('Es-tu sûr de vouloir supprimer ce sondage ? Cette action est irréversible.')) $wire.delete()"
                                            class="dropdown-item text-danger">
                                        Supprimer le Sondage
                                    </button>
                                @endcan
                            </div>
                        </div>
                    @endcanany
                </div>
            </div>

            @include("threads.polls.$panel")
        </div>
    </div>
@endif

@push('scripts')
    <script>
        Livewire.on('pollUpdated', () => {
            flash('Sondage mis à jour !');
        });
    </script>
@endpush
