@if(Route::is('threads.search'))
    <ais-search-box>
        <template #default="{ currentRefinement, isSearchStalled, refine }">
            <div class="input-group mb-4">
                <input type="search"
                       placeholder="Rechercher..."
                       class="form-control"
                       v-model="currentRefinement"
                       @input="refine($event.currentTarget.value)"
                       autofocus>

                <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit" title="Search">
                        <span v-if="isSearchStalled" v-cloak>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </span>

                        <svg v-else class="bi bi-search" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M10.442 10.442a1 1 0 011.415 0l3.85 3.85a1 1 0 01-1.414 1.415l-3.85-3.85a1 1 0 010-1.415z"
                                  clip-rule="evenodd"/>
                            <path fill-rule="evenodd"
                                  d="M6.5 12a5.5 5.5 0 100-11 5.5 5.5 0 000 11zM13 6.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </ais-search-box>

@else
    <form method="get" action="{{ route('threads.search') }}" class="input-group mb-4">
        <input type="search"
               placeholder="Rechercher..."
               name="query"
               class="form-control"
               required>
        <div class="input-group-append">
            <button class="btn btn-secondary" type="submit">
                <svg class="bi bi-search" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M10.442 10.442a1 1 0 011.415 0l3.85 3.85a1 1 0 01-1.414 1.415l-3.85-3.85a1 1 0 010-1.415z"
                          clip-rule="evenodd"/>
                    <path fill-rule="evenodd"
                          d="M6.5 12a5.5 5.5 0 100-11 5.5 5.5 0 000 11zM13 6.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"
                          clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </form>
@endif
