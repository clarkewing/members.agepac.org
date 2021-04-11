<div
    id="formModal"
    class="modal fade"
    role="dialog"
     data-backdrop="static"
    data-keyboard="false"
    wire:ignore.self
>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" wire:submit.prevent="save">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Sondage</h5>
            </div>

            <div class="modal-body px-lg-5 py-sm-4">
                <div class="form-group row mx-0">
                    <label for="title" class="col-sm-3 col-form-label">Titre</label>

                    <input type="text" class="col-sm-9 form-control" id="title"
                           placeholder="Un titre ou une question pour ton sondage"
                           wire:model.defer="state.title">

                    @error('state.title')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group row mx-0 mb-2">
                    <div class="col-sm-3 col-form-label">Options</div>

                    <div class="col-sm-9 px-0">
                        @foreach($state['options'] as $index => $option)
                            <div class="form-row align-items-center mb-2">
                                <div class="col-auto">
                                    <label class="sr-only" for="option_{{ $index }}_color">Couleur de l'option</label>
                                    <x-color-picker
                                        wire:model.defer="state.options.{{ $index }}.color"
                                        value="{{ $state['options'][$index]['color'] }}"
                                        id="option_{{ $index }}_color"
                                    />
                                    @error("state.options.$index.color")
                                        <div class="d-block invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col">
                                    <label class="sr-only"  for="option_{{ $index }}_label">Nom de l'option</label>
                                    <input type="text" class="form-control" id="option_{{ $index }}_label"
                                           placeholder="Nom de l'option"
                                           wire:model.defer="state.options.{{ $index }}.label">
                                    @error("state.options.$index.label")
                                        <div class="d-block invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-auto">
                                    <button type="button"
                                            class="btn btn-outline-secondary d-flex align-items-center p-1"
                                            @if(count($state['options']) <= 2) disabled @endif
                                            wire:click="removeOption({{ $index }})"
                                    >
                                        <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <button type="button"
                                class="btn btn-link"
                                wire:click="addOption"
                        >
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                            Ajouter une option
                        </button>
                    </div>

                    @error('state.options')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="offset-sm-3 mt-3 btn btn-link text-muted" type="button" data-toggle="collapse" data-target="#advancedParameters" aria-expanded="false" aria-controls="advancedParameters">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear-wide-connected mr-1" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8.932.727c-.243-.97-1.62-.97-1.864 0l-.071.286a.96.96 0 0 1-1.622.434l-.205-.211c-.695-.719-1.888-.03-1.613.931l.08.284a.96.96 0 0 1-1.186 1.187l-.284-.081c-.96-.275-1.65.918-.931 1.613l.211.205a.96.96 0 0 1-.434 1.622l-.286.071c-.97.243-.97 1.62 0 1.864l.286.071a.96.96 0 0 1 .434 1.622l-.211.205c-.719.695-.03 1.888.931 1.613l.284-.08a.96.96 0 0 1 1.187 1.187l-.081.283c-.275.96.918 1.65 1.613.931l.205-.211a.96.96 0 0 1 1.622.434l.071.286c.243.97 1.62.97 1.864 0l.071-.286a.96.96 0 0 1 1.622-.434l.205.211c.695.719 1.888.03 1.613-.931l-.08-.284a.96.96 0 0 1 1.187-1.187l.283.081c.96.275 1.65-.918.931-1.613l-.211-.205a.96.96 0 0 1 .434-1.622l.286-.071c.97-.243.97-1.62 0-1.864l-.286-.071a.96.96 0 0 1-.434-1.622l.211-.205c.719-.695.03-1.888-.931-1.613l-.284.08a.96.96 0 0 1-1.187-1.186l.081-.284c.275-.96-.918-1.65-1.613-.931l-.205.211a.96.96 0 0 1-1.622-.434L8.932.727zM8 12.997a4.998 4.998 0 1 0 0-9.995 4.998 4.998 0 0 0 0 9.996z"/>
                        <path fill-rule="evenodd" d="M7.375 8L4.602 4.302l.8-.6L8.25 7.5h4.748v1H8.25L5.4 12.298l-.8-.6L7.376 8z"/>
                    </svg>
                    Paramètres avancés
                </button>

                <div class="collapse row pt-2" id="advancedParameters" wire:ignore.self>
                    <div class="form-group offset-sm-3 col-sm-9">
                        <label class="d-block">
                            Les utilisateurs peuvent modifier leur vote
                        </label>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="votes_editable" value="1"
                                   wire:model.defer="state.votes_editable"
                                   id="votes_editable_true">
                            <label class="form-check-label" for="votes_editable_true">Oui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="votes_editable" value="0"
                                   wire:model.defer="state.votes_editable"
                                   id="votes_editable_false">
                            <label class="form-check-label" for="votes_editable_false">Non</label>
                        </div>

                        @error('state.votes_editable')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group offset-sm-3 col-sm-9">
                        <label class="d-block">
                            Nombre d'options sélectionnables
                        </label>

                        <div class="row mx-0">
                            <input type="number" class="col-3 col-md-2 form-control" id="max_votes"
                                   wire:model.defer="state.max_votes"
                                   @if($maxVotesNull) disabled @endif
                            />

                            <div class="col-auto form-check form-check-inline ml-3">
                                <input class="form-check-input" type="checkbox"
                                       name="maxVotesNull" value="1"
                                       wire:model="maxVotesNull"
                                       id="maxVotesNull">
                                <label class="form-check-label" for="maxVotesNull">Pas de limite</label>
                            </div>
                        </div>

                        @error('state.max_votes')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group offset-sm-3 col-sm-9">
                        <label class="d-block">
                            Les personnes pouvant voir qui a voté pour quelle option
                        </label>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="votes_privacy" value="public"
                                   wire:model.defer="state.votes_privacy"
                                   id="votes_privacy_public">
                            <label class="form-check-label" for="votes_privacy_public">Tous les utilisateurs</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="votes_privacy" value="private"
                                   wire:model.defer="state.votes_privacy"
                                   id="votes_privacy_private">
                            <label class="form-check-label" for="votes_privacy_private">Moi seulement</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="votes_privacy" value="anonymous"
                                   wire:model.defer="state.votes_privacy"
                                   id="votes_privacy_anonymous">
                            <label class="form-check-label" for="votes_privacy_anonymous">Personne</label>
                        </div>

                        @error('state.votes_privacy')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group offset-sm-3 col-sm-9">
                        <label class="d-block">
                            Les utilisateurs peuvent voir les résultats sans voter
                        </label>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="results_before_voting" value="1"
                                   wire:model.defer="state.results_before_voting"
                                   id="results_before_voting_true">
                            <label class="form-check-label" for="results_before_voting_true">Oui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="results_before_voting" value="0"
                                   wire:model.defer="state.results_before_voting"
                                   id="results_before_voting_false">
                            <label class="form-check-label" for="results_before_voting_false">Non</label>
                        </div>

                        @error('state.results_before_voting')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group offset-sm-3 col-sm-9">
                        <label class="d-block">
                            Clôture des votes
                        </label>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="lockedAtNull" value="1"
                                   wire:model="lockedAtNull"
                                   id="lockedAtNull_true">
                            <label class="form-check-label" for="lockedAtNull_true">Jamais</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="lockedAtNull" value="0"
                                   wire:model="lockedAtNull"
                                   id="lockedAtNull_false">
                            <label class="form-check-label" for="lockedAtNull_false">À une certaine date</label>
                        </div>

                        @unless($lockedAtNull)
                            <div class="d-flex mt-3">
                                <x-date-picker
                                    wire:model.defer="state.locked_at"
                                    :options="['enableTime' => true]"
                                />

                                <div class="font-weight-bold text-muted pl-3">
                                    Date et heure en UTC.
                                </div>
                            </div>
                        @endunless

                        @error('state.locked_at')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary mr-auto"
                        wire:loading.attr="disabled" data-dismiss="modal">
                    Annuler
                </button>

                <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm mr-1" role="status">
                            <span class="sr-only">Loading...</span>
                        </span>
                        Bip-boop-bop
                    </span>

                    <span wire:loading.remove>Sauvegarder</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('showForm', () => {
            $('#formModal').modal('show');
        });
        Livewire.on('pollUpdated', () => {
            $('#formModal').modal('hide');
        });
        Livewire.on('pollDeleted', () => {
            flash('Sondage supprimé.');
        });
    </script>
@endpush
