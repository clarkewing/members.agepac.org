<div class="row no-gutters pt-2 mb-3">
    <div class="col border-bottom">
        <h3 class="h5 text-blue d-flex align-items-center mb-1 thread-title" v-if="! editing">
            <svg v-if="pinned"
                 class="bi bi-flag-fill text-orange" width="1em" height="1em" viewBox="0 0 16 16"
                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3.5 1a.5.5 0 01.5.5v13a.5.5 0 01-1 0v-13a.5.5 0 01.5-.5z"
                      clip-rule="evenodd"/>
                <path fill-rule="evenodd"
                      d="M3.762 2.558C4.735 1.909 5.348 1.5 6.5 1.5c.653 0 1.139.325 1.495.562l.032.022c.391.26.646.416.973.416.168 0 .356-.042.587-.126a8.89 8.89 0 00.593-.25c.058-.027.117-.053.18-.08.57-.255 1.278-.544 2.14-.544a.5.5 0 01.5.5v6a.5.5 0 01-.5.5c-.638 0-1.18.21-1.734.457l-.159.07c-.22.1-.453.205-.678.287A2.719 2.719 0 019 9.5c-.653 0-1.139-.325-1.495-.562l-.032-.022c-.391-.26-.646-.416-.973-.416-.833 0-1.218.246-2.223.916A.5.5 0 013.5 9V3a.5.5 0 01.223-.416l.04-.026z"
                      clip-rule="evenodd"/>
            </svg>

            <span v-text="title"></span>

            <button class="btn btn-sm lh-1 ml-1 p-1"
                    v-if="(authorize('owns', thread) && ! locked) || authorize('hasPermission', 'threads.edit')"
                    @click="editing = true">
                <span class="sr-only">Modifier</span>
                <svg class="bi bi-pencil" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M11.293 1.293a1 1 0 011.414 0l2 2a1 1 0 010 1.414l-9 9a1 1 0 01-.39.242l-3 1a1 1 0 01-1.266-1.265l1-3a1 1 0 01.242-.391l9-9zM12 2l2 2-9 9-3 1 1-3 9-9z" clip-rule="evenodd"/>
                    <path fill-rule="evenodd" d="M12.146 6.354l-2.5-2.5.708-.708 2.5 2.5-.707.708zM3 10v.5a.5.5 0 00.5.5H4v.5a.5.5 0 00.5.5H5v.5a.5.5 0 00.5.5H6v-1.5a.5.5 0 00-.5-.5H5v-.5a.5.5 0 00-.5-.5H3z" clip-rule="evenodd"/>
                </svg>
            </button>
        </h3>

        <div class="row no-gutters mb-2" v-else>
            <div class="col form-group mb-0">
                <input type="text" class="form-control" v-model="form.title" placeholder="Titre">
            </div>

            <div class="col-auto form-group d-flex mb-0 pl-2">
                <!-- TODO: Determine if we need a delete button, or if Nova suffices -->
                {{--            <button class="btn btn-sm btn-outline-danger mr-2" @click="destroy">Supprimer</button>--}}

                <button class="btn btn-sm btn-link ml-auto mr-2" @click="resetForm" type="button">Annuler</button>
                <button class="btn btn-sm btn-success" @click="update">Sauvegarder</button>
            </div>
        </div>

        <div class="d-flex align-items-center small mb-3">
            <p class="mb-0">
                Publié par :
                <a href="{{ route('profiles.show', $thread->creator) }}">
                    {{ $thread->creator->name }}
                    ({{ $thread->creator->reputation }} XP)
                </a>
            </p>

            <button :class="[classes('locked'), 'btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal']"
                    @click="toggleLock"
                    v-if="authorize('hasPermission', locked ? 'threads.unlock' : 'threads.lock')"
                    v-text="locked ? 'Dévérouiller' : 'Vérouiller'"></button>

            <button :class="[classes('pinned'), 'btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal']"
                    @click="togglePin"
                    v-if="authorize('hasPermission', pinned ? 'threads.unpin' : 'threads.pin')"
                    v-text="pinned ? 'Désépingler' : 'Épingler'"></button>

            <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}"
                              v-if="signedIn"></subscribe-button>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .thread-title .btn {
            color: inherit;
            opacity: 0;
            transition: opacity .15s;
        }
        .thread-title:hover .btn {
            opacity: 1;
        }
    </style>
@endpush
