@extends('layouts.app')

@section('content')
    <instant-search inline-template>
        <ais-instant-search :search-client="searchClient" index-name="companies" :routing="routing">
            <ais-configure query="{{ Request::query('query') }}"></ais-configure>

            <div class="container text-primary bg-white mt-n4 py-4 border border-top-0 rounded-bottom">
                <div class="row mb-3 mb-md-5">
                    <div class="d-flex align-items-center col-lg-10 offset-lg-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent pl-0 mb-0">
                                <li class="breadcrumb-item">
                                    <span>Annuaire des compagnies</span>
                                </li>
                            </ol>
                        </nav>

                        <create-company class="ml-auto"></create-company>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3 offset-md-0 offset-lg-1">
                        <ais-search-box>
                            <template #default="{ currentRefinement, isSearchStalled, refine }">
                                <div class="mb-4">
                                    <div class="d-flex">
                                        <label class="sr-only" for="search">Recherche</label>

                                        <div class="input-group">
                                            <input type="search"
                                                   class="form-control"
                                                   id="search"
                                                   placeholder="Rechercher..."
                                                   name="query"
                                                   v-model="currentRefinement"
                                                   @input="refine($event.target.value)">

                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-secondary">
                                                    <span v-if="isSearchStalled" v-cloak>
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                              aria-hidden="true"></span>
                                                        <span class="sr-only">Loading...</span>
                                                    </span>

                                                    <svg v-else class="bi bi-search" width="1em" height="1em"
                                                         viewBox="0 0 16 16" fill="currentColor"
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
                                    </div>

                                    <div class="d-flex d-md-none mt-2">
                                        <button class="flex-even btn btn-light mr-2"
                                                type="button"
                                                data-toggle="collapse" data-target="#sortBy">
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-sort-down"
                                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                      d="M3 2a.5.5 0 0 1 .5.5v10a.5.5 0 0 1-1 0v-10A.5.5 0 0 1 3 2z"/>
                                                <path fill-rule="evenodd"
                                                      d="M5.354 10.146a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L3 11.793l1.646-1.647a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 9a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                                            </svg>
                                            <span>Trier</span>
                                        </button>

                                        <button class="flex-even btn btn-light"
                                                type="button"
                                                data-toggle="collapse" data-target="#filterBy">
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-filter"
                                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                      d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                                            </svg>
                                            <span>Filtrer</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </ais-search-box>

                        <div class="accordion" id="refinements">
                            <div class="collapse d-md-block" id="sortBy" data-parent="#refinements">
                                <ais-sort-by :items="[
                                { value: 'companies', label: 'Nom ascendant' },
                            ]">
                                    <template #default="{items, currentRefinement, refine}">
                                        <x-linklist title="Trier par" spacing="1">
                                            <li v-for="item in items" :key="item.value" class="form-check">
                                                <label class="form-check-label"
                                                       :class="{ 'font-weight-bolder': item.value === currentRefinement }">
                                                    <input type="radio"
                                                           class="form-check-input"
                                                           :value="item.value"
                                                           :checked="item.value === currentRefinement"
                                                           @change="refine(item.value)">
                                                    <span v-text="item.label"></span>
                                                </label>
                                            </li>
                                        </x-linklist>
                                    </template>
                                </ais-sort-by>
                            </div>

                            <div class="collapse d-md-block" id="filterBy" data-parent="#refinements">
                                <ais-refinement-list attribute="type">
                                    <template #default="{items, refine, createURL}">
                                        <x-linklist title="Filtrer par type" spacing="1">
                                            <li v-for="item in items" :key="item.value" class="form-check">
                                                <label class="form-check-label"
                                                       :class="{ 'font-weight-bolder': item.isRefined }">
                                                    <input type="checkbox"
                                                           class="form-check-input"
                                                           :value="item.value"
                                                           :checked="item.isRefined"
                                                           @change="refine(item.value)">
                                                    <span v-text="item.label"></span>
                                                    <span class="badge badge-secondary ml-1" v-text="item.count"></span>
                                                </label>
                                            </li>
                                        </x-linklist>
                                    </template>
                                </ais-refinement-list>
                            </div>
                        </div>

                        <ais-powered-by class="d-none d-md-block"></ais-powered-by>
                    </div>

                    <div class="col-md-8 col-lg-7">
                        <ais-hits class="mt-md-n3">
                            <template #default="{ items }">
                                <company-result v-for="company in items"
                                                :key="company.objectID"
                                                v-bind="company"
                                ></company-result>
                            </template>
                        </ais-hits>

                        <ais-pagination @page-change="onPageChange">
                            <template
                                #default="{ currentRefinement, nbPages, pages, isFirstPage, isLastPage, refine, createURL }">
                                <ul class="pagination justify-content-center mt-5">
                                    <li :class="['page-item', isFirstPage ? 'disabled' : '']">
                                        <a class="page-link"
                                           :href="createURL(currentRefinement - 1)"
                                           @click.prevent="refine(currentRefinement - 1)">
                                            ‹
                                        </a>
                                    </li>
                                    <li class="page-item" v-for="page in pages" :key="page">
                                        <a :class="['page-link', page === currentRefinement ? 'active' : '']"
                                           :href="createURL(page)"
                                           @click.prevent="refine(page)"
                                           v-text="page + 1">
                                        </a>
                                    </li>
                                    <li :class="['page-item', isLastPage ? 'disabled' : '']">
                                        <a class="page-link"
                                           :href="createURL(currentRefinement + 1)"
                                           @click.prevent="refine(currentRefinement + 1)">
                                            ›
                                        </a>
                                    </li>
                                </ul>
                            </template>
                        </ais-pagination>
                    </div>
                </div>
            </div>

        </ais-instant-search>
    </instant-search>
@endsection

@push('scripts')
    <script>
        window.App.companyTypes = @json(App\Company::typeStrings());
    </script>
@endpush
