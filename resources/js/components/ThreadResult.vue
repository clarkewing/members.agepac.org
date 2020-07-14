<template>
    <div class="row no-gutters flex-nowrap w-100 pt-2 mb-4">
        <div class="d-flex pr-3">
            <img :src="thread.creator.avatar_path"
                 :alt="thread.creator.name"
                 class="rounded-circle cover"
                 style="width: 2.5rem; height: 2.5rem;">
        </div>

        <div class="d-flex flex-column flex-grow-1 border-gray-700 border-bottom">
            <h3 class="h5 mb-1">
                <svg v-if="thread.pinned" class="bi bi-flag-fill text-orange" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M3.5 1a.5.5 0 01.5.5v13a.5.5 0 01-1 0v-13a.5.5 0 01.5-.5z" clip-rule="evenodd"/>
                    <path fill-rule="evenodd" d="M3.762 2.558C4.735 1.909 5.348 1.5 6.5 1.5c.653 0 1.139.325 1.495.562l.032.022c.391.26.646.416.973.416.168 0 .356-.042.587-.126a8.89 8.89 0 00.593-.25c.058-.027.117-.053.18-.08.57-.255 1.278-.544 2.14-.544a.5.5 0 01.5.5v6a.5.5 0 01-.5.5c-.638 0-1.18.21-1.734.457l-.159.07c-.22.1-.453.205-.678.287A2.719 2.719 0 019 9.5c-.653 0-1.139-.325-1.495-.562l-.032-.022c-.391-.26-.646-.416-.973-.416-.833 0-1.218.246-2.223.916A.5.5 0 013.5 9V3a.5.5 0 01.223-.416l.04-.026z" clip-rule="evenodd"/>
                </svg>

                <a :href="thread.path">
                    <ais-highlight :hit="data" attribute="thread.title" v-if="isInstantSearchResult"></ais-highlight>
                    <span v-text="thread.title" v-else></span>
                </a>
            </h3>

            <p class="small mb-3">
                Publié par : <a :href="'/profiles/' + thread.creator.username" v-text="thread.creator.name"></a>
            </p>

            <p class="mb-4"
               v-if="isInstantSearchResult">
                <span v-if="! bodyStartsWithSnippet(data)">...</span>
                <ais-snippet :hit="data" attribute="body"></ais-snippet>
                <span v-if="! bodyEndsWithSnippet(data)">...</span>
            </p>

            <p class="mb-4"
               v-else
               v-text="striptags(thread.snippet)"
               v-line-clamp="3" style="word-break: normal !important;">
            </p>

            <div class="d-flex flex-wrap align-items-center small mb-4">
                <a class="btn btn-sm bg-gray-300 mr-3"
                   :href="'/threads/' + thread.channel.slug"
                   v-text="thread.channel.name"
                ></a>

                <!-- Hide visits when using instant search (unreliable) -->
                <div class="d-flex align-items-center mr-3" v-if="! isInstantSearchResult">
                    <svg class="bi bi-eye-fill text-gray-300 mr-2" width="1.5em" height="1.5em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.5 8a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z" clip-rule="evenodd"/>
                    </svg>
                    {{ thread.visits }} {{ 'visite' | pluralize(thread.visits) }}
                </div>

                <div class="d-flex align-items-center mr-3">
                    <svg class="bi bi-chat-fill text-gray-300 mr-2" width="1.5em" height="1.5em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 008 15z"/>
                    </svg>
                    {{ thread.replies_count }} {{ 'réponse' | pluralize(thread.replies_count) }}
                </div>

                <a class="btn btn-sm btn-outline-secondary px-4 ml-auto d-none d-md-inline-block"
                   :href="thread.path"
                >
                    voir plus
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['data'],

        computed: {
            isInstantSearchResult: function () {
                return this.data.__position !== undefined;
            },

            thread: function () {
                return this.isInstantSearchResult
                    ? this.data.thread
                    : this.data;
            }
        },

        methods: {
            bodyStartsWithSnippet: function (hit) {
                return this.striptags(hit.body)
                    .startsWith(
                        _.unescape(this.striptags(hit._snippetResult.body.value))
                    );
            },

            bodyEndsWithSnippet: function (hit) {
                return this.striptags(hit.body)
                    .endsWith(
                        _.unescape(this.striptags(hit._snippetResult.body.value))
                    );
            }
        }
    }
</script>
