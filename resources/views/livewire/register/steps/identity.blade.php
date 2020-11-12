<div role="tabpanel"
     class="tab-pane fade {{ $active ? 'active show' : null }}">

    @if($invitationNotFound)
        <div class="text-center">
            <svg class="bi bi-exclamation-octagon-fill text-danger mb-2" width="3em" height="3em"
                 viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M11.46.146A.5.5 0 0011.107 0H4.893a.5.5 0 00-.353.146L.146 4.54A.5.5 0 000 4.893v6.214a.5.5 0 00.146.353l4.394 4.394a.5.5 0 00.353.146h6.214a.5.5 0 00.353-.146l4.394-4.394a.5.5 0 00.146-.353V4.893a.5.5 0 00-.146-.353L11.46.146zM8 4a.905.905 0 00-.9.995l.35 3.507a.552.552 0 001.1 0l.35-3.507A.905.905 0 008 4zm.002 6a1 1 0 100 2 1 1 0 000-2z"
                      clip-rule="evenodd"/>
            </svg>

            <h6 class="text-danger mb-4">New phone who dis?</h6>

            <p>
                L'inscription à l'AGEPAC est exclusivement ouverte aux Élèves Pilotes de Ligne (EPL) de
                l'ENAC.
            </p>

            <p class="small mb-4">
                Les facteurs humains sont responsables de 4 accidents aériens sur 5.<br>
                Si tu penses qu'il s'agit d'une erreur, on est là pour t'aider !
            </p>

            <div class="d-flex justify-content-between">
                <button class="btn btn-outline-secondary rounded-pill" wire:click="$set('invitationNotFound', false)">
                    <svg class="bi bi-arrow-left-short mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M7.854 4.646a.5.5 0 010 .708L5.207 8l2.647 2.646a.5.5 0 01-.708.708l-3-3a.5.5 0 010-.708l3-3a.5.5 0 01.708 0z"
                              clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M4.5 8a.5.5 0 01.5-.5h6.5a.5.5 0 010 1H5a.5.5 0 01-.5-.5z"
                              clip-rule="evenodd"/>
                    </svg>
                    <span>Retour</span>
                </button>

                <a class="btn btn-outline-info rounded-pill" href="/help">
                    <svg class="bi bi-life-preserver mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm0 1A8 8 0 108 0a8 8 0 000 16z"
                              clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M8 11a3 3 0 100-6 3 3 0 000 6zm0 1a4 4 0 100-8 4 4 0 000 8z"
                              clip-rule="evenodd"/>
                        <path
                            d="M11.642 6.343L15 5v6l-3.358-1.343A3.99 3.99 0 0012 8a3.99 3.99 0 00-.358-1.657zM9.657 4.358L11 1H5l1.343 3.358A3.985 3.985 0 018 4c.59 0 1.152.128 1.657.358zM4.358 6.343L1 5v6l3.358-1.343A3.985 3.985 0 014 8c0-.59.128-1.152.358-1.657zm1.985 5.299L5 15h6l-1.343-3.358A3.984 3.984 0 018 12a3.99 3.99 0 01-1.657-.358z"/>
                    </svg>
                    <span>Aide</span>
                </a>
            </div>
        </div>

    @elseif(is_null($invitation))
        @unless($hasSearchedByName)
            <form wire:submit.prevent="searchInvitation">

                <div class="form-group">
                    <label for="name">
                        Comment t'appelles-tu ?
                    </label>

                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           wire:model.defer="name"
                           autocomplete="name"
                           placeholder="Marc Houalla"
                           required
                           autofocus>

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary rounded-pill d-flex align-items-center mx-auto">
                    <span>Continuer</span>

                    <svg class="bi bi-arrow-right-short ml-2" width="1em" height="1em" viewBox="0 0 16 16"
                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M8.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L10.793 8 8.146 5.354a.5.5 0 010-.708z"
                              clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M4 8a.5.5 0 01.5-.5H11a.5.5 0 010 1H4.5A.5.5 0 014 8z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </form>

        @else
            <h5 class="text-center mb-3">
                Dis-nous en plus...
            </h5>


            <form wire:submit.prevent="searchInvitation">

                <div class="form-group">
                    <label for="first_name">
                        Prénom
                    </label>

                    <input type="text"
                           class="form-control @error('first_name') is-invalid @enderror"
                           id="first_name"
                           wire:model.defer="first_name"
                           autocomplete="given-name"
                           placeholder="Marc"
                           required>

                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name">
                        Nom
                    </label>

                    <input type="text"
                           class="form-control @error('last_name') is-invalid @enderror"
                           id="last_name"
                           wire:model.defer="last_name"
                           autocomplete="family-name"
                           placeholder="Houalla"
                           required>

                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="class_course">
                        Cursus
                    </label>

                    <select class="form-control @error('class_course') is-invalid @enderror"
                            id="class_course"
                            wire:model.defer="class_course"
                            required>

                        <option value="" disabled></option>

                        @foreach(config('council.courses') as $courseOption)
                            <option value="{{ $courseOption }}">
                                {{ $courseOption }}
                            </option>
                        @endforeach
                    </select>

                    @error('class_course')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="class_year">
                        Promotion
                    </label>

                    <input type="number"
                           min="1900" max="2199" step="1"
                           class="form-control @error('class_year') is-invalid @enderror"
                           id="class_year"
                           wire:model.defer="class_year"
                           required>

                    @error('class_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit"
                        class="btn btn-primary rounded-pill d-flex align-items-center mx-auto">
                    <span>Continuer</span>

                    <svg class="bi bi-arrow-right-short ml-2" width="1em" height="1em" viewBox="0 0 16 16"
                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M8.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L10.793 8 8.146 5.354a.5.5 0 010-.708z"
                              clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M4 8a.5.5 0 01.5-.5H11a.5.5 0 010 1H4.5A.5.5 0 014 8z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </form>
        @endunless

    @else
        <h5 class="text-center mb-3">
            Trouvé !
        </h5>

        <div>
            <dl class="mb-4">
                <dt>Prénom</dt>
                <dd>{{ $first_name }}</dd>

                <dt>Nom</dt>
                <dd>{{ $last_name }}</dd>

                <dt>Promotion</dt>
                <dd>{{ $class_course }} {{ $class_year }}</dd>
            </dl>

            <button type="button"
                    class="btn btn-primary rounded-pill d-flex align-items-center mx-auto mb-2"
                    wire:click="run">
                <span>C'est bien moi</span>

                <svg class="bi bi-arrow-right-short ml-2" width="1em" height="1em" viewBox="0 0 16 16"
                     fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M8.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L10.793 8 8.146 5.354a.5.5 0 010-.708z"
                          clip-rule="evenodd"/>
                    <path fill-rule="evenodd" d="M4 8a.5.5 0 01.5-.5H11a.5.5 0 010 1H4.5A.5.5 0 014 8z"
                          clip-rule="evenodd"/>
                </svg>
            </button>

            <button type="button"
                    class="btn btn-link d-flex align-items-center mx-auto"
                    wire:click="searchAgain">
                <span>Ce n'est pas moi</span>
            </button>
        </div>
    @endif
</div>
