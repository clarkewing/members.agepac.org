@if(Route::is('threads.search'))
    <ais-search-box class="flex-grow-1 flex-sm-grow-0" id="forumSearchbar">
        <template #default="{ currentRefinement, isSearchStalled, refine }">
            <div class="flex-grow-1 mb-sm-4">
                <expanding-search-bar type="search"
                                      classes="ml-auto"
                                      placeholder="Rechercher..."
                                      name="query"
                                      v-model="currentRefinement"
                                      @input="refine($event.target.value)">
                    <template #btn>
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
                    </template>
                </expanding-search-bar>
            </div>
        </template>
    </ais-search-box>

@else
    <form method="get" action="{{ route('threads.search') }}" class="flex-grow-1 flex-sm-grow-0 mb-sm-4" id="forumSearchbar">
        <expanding-search-bar type="search"
                              classes="ml-auto"
                              placeholder="Rechercher..."
                              name="query"
                              btn-type="submit"
                              required>
            <template #btn>
                <svg class="bi bi-search" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M10.442 10.442a1 1 0 011.415 0l3.85 3.85a1 1 0 01-1.414 1.415l-3.85-3.85a1 1 0 010-1.415z"
                          clip-rule="evenodd"/>
                    <path fill-rule="evenodd"
                          d="M6.5 12a5.5 5.5 0 100-11 5.5 5.5 0 000 11zM13 6.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"
                          clip-rule="evenodd"/>
                </svg>
            </template>
        </expanding-search-bar>
    </form>
@endif
