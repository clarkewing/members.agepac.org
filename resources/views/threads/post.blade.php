<div class="row no-gutters pt-2 mb-4" id="post-{{ $post->id }}">
    <div class="col-auto flex-column pr-3">
        <img src="{{ $post->owner->avatar_path }}"
             alt="{{ $post->owner->name }}"
             class="rounded-circle cover mb-4"
             style="width: 2.5rem; height: 2.5rem;">

        <div>
            <button
                type="button"
                class="btn d-block shadow-none p-0 mx-auto {{ $post->is_favorited ? 'text-info' : 'text-gray-400' }}"
                wire:click="toggleFavorite"
                wire:loading.attr="disabled"
            >
                <svg class="bi bi-heart-fill"
                     width="1.125rem" height="1.125rem"
                     viewBox="0 0 16 16"
                     fill="currentColor"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"
                          clip-rule="evenodd"/>
                </svg>
            </button>

            <button
                class="btn d-block mx-auto mb-0 p-0 {{ $post->is_favorited ? 'text-info' : 'text-gray-400' }}"
                type="button" data-toggle="modal" data-target="#favorites{{ $post->id }}"
            >
                {{ $post->favorites_count }}
            </button>

            <div class="modal fade" id="favorites{{ $post->id }}" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-body">
                            @if($post->favorites->isEmpty())
                                <p class="mb-0 text-center">Aucune réaction à ce post.</p>
                            @else
                                <ul>
                                    @foreach($post->favorites as $favorite)
                                        <li>
                                            <a href="{{ route('profiles.show', $favorite->owner) }}">
                                                {{ $favorite->owner->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col border-bottom">
        <h4 class="h6 mb-1"><a href="/profiles/{{ $post->owner->username }}">{{ $post->owner->name }}</a></h4>

        <div class="d-flex align-items-center small mb-3">
            <p class="mb-0">{{ $post->created_at->diffForHumans() }}</p>

            @unless($post->trashed())
                @can('update', $post)
                    <button class="btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal text-muted"
                            wire:click="$set('editing', true)">
                        Modifier
                    </button>
                @endcan

                @can('update', $post->thread)
                    <button
                        class="btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal text-muted"
                        wire:click="toggleBestPost"
                        wire:loading.attr="disabled"
                    >
                        @if($post->is_best) Enlever @else Marquer @endif comme meilleure réponse
                    </button>
                @endcan

            @else
                @if($showDeleted)
                    @can('restore', $post)
                        <button class="btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal text-muted"
                                wire:click="restore">
                            Rétablir
                        </button>
                    @endcan
                @endif
            @endunless
        </div>

        @if($post->trashed())
            @if($showDeleted)
                <p class="trix-content mb-4">
                    {!! $post->body !!}
                </p>
            @else
                <div class="text-muted d-flex align-items-center justify-content-center">
                    Cette réponse a été supprimée.

                    <button class="btn btn-link rounded-0 p-0 font-size-normal text-muted ml-1"
                            wire:click="$set('showDeleted', true)">
                        <u>La voir ?</u>
                    </button>
                </div>
            @endif

        @else
            @if($editing)
{{--                <form @submit.prevent="update">--}}
{{--                    <div class="form-group">--}}
{{--                        <wysiwyg ref="wysiwyg" v-model="tempBody"></wysiwyg>--}}
{{--                    </div>--}}
{{--    --}}
{{--                    <div class="form-group d-flex">--}}
{{--                        @unless($post->isThreadInitiator())--}}
{{--                            <button class="btn btn-sm btn-outline-danger mr-2"--}}
{{--                                    type="button"--}}
{{--                                    v-if="! isThreadInitiator"--}}
{{--                                    @click="destroy">--}}
{{--                                Supprimer--}}
{{--                            </button>--}}
{{--                        @endunless--}}
{{--    --}}
{{--                        <button class="btn btn-sm btn-link ml-auto mr-2" @click="cancel">Annuler</button>--}}
{{--                        <button class="btn btn-sm btn-success" type="submit">Sauvegarder</button>--}}
{{--                    </div>--}}
{{--                </form>--}}

            @else
                @if($post->is_best)
                    <h6 class="text-success d-flex align-items-center">
                        <div class="rounded-circle text-center text-white bg-success mr-1" style="width: 1.5em; height: 1.5em;">
                            <svg class="bi bi-star-fill" style="margin-bottom: -.125em;" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </div>
                        <span>Meilleure réponse</span>
                    </h6>
                @endif

                <p class="trix-content mb-4">
                    {!! $post->body !!}
                </p>
            @endif
        @endif
    </div>
</div>
