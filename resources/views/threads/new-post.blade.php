<div>
    @if($thread->locked)
        <div class="d-flex text-danger px-5 py-3 rounded border-placeholder border-danger align-items-center">
            <svg class="bi bi-lock-fill mr-2" width="1.5em" height="1.5em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <rect width="11" height="9" x="2.5" y="7" rx="2"/>
                <path fill-rule="evenodd" d="M4.5 4a3.5 3.5 0 117 0v3h-1V4a2.5 2.5 0 00-5 0v3h-1V4z" clip-rule="evenodd"/>
            </svg>
            <p class="mb-0">Cette discussion a été vérouillée. Il n'est plus possible d'y répondre.</p>
        </div>

    @elseif(! Auth::user()->hasVerifiedEmail())
        <div class="d-flex text-danger px-5 py-3 rounded border-placeholder border-danger align-items-center">
            <svg class="bi bi-exclamation-triangle-fill mr-2" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8.982 1.566a1.13 1.13 0 00-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5a.905.905 0 00-.9.995l.35 3.507a.552.552 0 001.1 0l.35-3.507A.905.905 0 008 5zm.002 6a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
            </svg>
            <p class="mb-0">Tu dois <a href="/email/verify">vérifier ton adresse email</a> avant de pouvoir participer aux discussions.</p>
        </div>

    @else
        <div class="mt-3">
            @if($editing)
                <div class="form-group">
{{--                    <wysiwyg ref="wysiwyg" name="body" v-model="body" placeholder="Quelque chose à ajouter ?" required></wysiwyg>--}}
                    <textarea placeholder="Quelque chose à ajouter ?"></textarea>
                </div>

                <div class="form-group d-flex">
                    <button class="btn btn-link ml-auto mr-2" wire:click="$set('editing', false)">Annuler</button>
                    <button class="btn btn-success" wire:click="store">Publier</button>
                </div>

            @else
                <button type="button" class="btn btn-block border-placeholder p-5 d-flex align-items-center" wire:click="$set('editing', true)">
                    <img src="{{ Auth::user()->avatar_path }}"
                         alt="{{ Auth::user()->name }}"
                         class="rounded-circle cover mr-3"
                         style="width: 2.5rem; height: 2.5rem;">

                    <p class="mb-0">Participer à la discussion</p>
                </button>
            @endif
        </div>
    @endif
</div>
