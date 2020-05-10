{{-- Editing --}}
<div class="row no-gutters pt-2 mb-4" v-if="editing">
    <div class="col-auto pr-3">
        <img src="{{ $thread->creator->avatar_path }}"
             alt="{{ $thread->creator->name }}"
             class="rounded-circle cover"
             style="width: 2.5rem; height: 2.5rem;">
    </div>

    <div class="col border-bottom">
        <div class="form-group">
            <input type="text" class="form-control" v-model="form.title" placeholder="Titre">
        </div>

        <div class="form-group">
            <wysiwyg ref="wysiwyg" v-model="form.body" placeholder="Lâche-toi ! Qu'as-tu à dire ?"></wysiwyg>
        </div>

        <div class="form-group d-flex">
            <!-- TODO: Determine if we need a delete button, or if Nova suffices -->
{{--            <button class="btn btn-sm btn-outline-danger mr-2" @click="destroy">Supprimer</button>--}}

            <button class="btn btn-sm btn-link ml-auto mr-2" @click="resetForm" type="button">Annuler</button>
            <button class="btn btn-sm btn-success" @click="update">Sauvegarder</button>
        </div>
    </div>
</div>

{{-- Viewing --}}
<div class="row no-gutters pt-2 mb-4" v-else>
    <div class="col-auto pr-3">
        <img src="{{ $thread->creator->avatar_path }}"
             alt="{{ $thread->creator->name }}"
             class="rounded-circle cover"
             style="width: 2.5rem; height: 2.5rem;">
    </div>

    <div class="col border-bottom">
        <h3 class="h5 mb-1">
            <svg v-if="pinned"
                 class="bi bi-flag-fill text-orange" width="1em" height="1em" viewBox="0 0 16 16"
                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3.5 1a.5.5 0 01.5.5v13a.5.5 0 01-1 0v-13a.5.5 0 01.5-.5z"
                      clip-rule="evenodd"/>
                <path fill-rule="evenodd"
                      d="M3.762 2.558C4.735 1.909 5.348 1.5 6.5 1.5c.653 0 1.139.325 1.495.562l.032.022c.391.26.646.416.973.416.168 0 .356-.042.587-.126a8.89 8.89 0 00.593-.25c.058-.027.117-.053.18-.08.57-.255 1.278-.544 2.14-.544a.5.5 0 01.5.5v6a.5.5 0 01-.5.5c-.638 0-1.18.21-1.734.457l-.159.07c-.22.1-.453.205-.678.287A2.719 2.719 0 019 9.5c-.653 0-1.139-.325-1.495-.562l-.032-.022c-.391-.26-.646-.416-.973-.416-.833 0-1.218.246-2.223.916A.5.5 0 013.5 9V3a.5.5 0 01.223-.416l.04-.026z"
                      clip-rule="evenodd"/>
            </svg>

            <span class="text-blue" v-text="title"></span>
        </h3>

        <div class="d-flex align-items-center small mb-3">
            <p class="mb-0">Publié par : <a
                    href="{{ route('profiles.show', $thread->creator) }}">{{ $thread->creator->name }}
                    ({{ $thread->creator->reputation }} XP)</a></p>

            <button class="btn btn-link border-left p-0 pl-2 ml-2 font-size-normal text-muted"
                    v-if="authorize('owns', thread) && ! locked"
                    @click="editing = true">
                Modifier
            </button>

            <button :class="[classes('locked'), 'btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal']"
                    @click="toggleLock"
                    v-if="authorize('isAdmin')"
                    v-text="locked ? 'Dévérouiller' : 'Vérouiller'"></button>

            <button :class="[classes('pinned'), 'btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal']"
                    @click="togglePin"
                    v-if="authorize('isAdmin')"
                    v-text="pinned ? 'Désépingler' : 'Épingler'"></button>

            <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}"
                              v-if="signedIn"></subscribe-button>
        </div>

{{--        <p class="mb-4" v-html="body"></p>--}}
    </div>
</div>
