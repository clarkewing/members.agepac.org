<template>
    <ul class="pagination justify-content-center" v-if="shouldPaginate">
        <li :class="['page-item', ! prevUrl ? 'disabled' : '']">
            <a class="page-link"
               :href="prevUrl"
               @click.prevent="currentPage--"
               rel="prev"
            >
                ‹
            </a>
        </li>
        <li class="page-item" v-for="page in pages" :key="page">
            <a :class="['page-link', page === currentPage ? 'active' : '']"
               :href="createURL(page)"
               @click.prevent="currentPage = page"
               v-text="page">
            </a>
        </li>
        <li :class="['page-item', ! nextUrl ? 'disabled' : '']">
            <a class="page-link"
               :href="nextUrl"
               @click.prevent="currentPage++"
               rel="next"
            >
                ›
            </a>
        </li>
    </ul>
</template>

<script>
    export default {
        props: ['dataSet'],

        data() {
            return {
                currentPage: 1,
                lastPage: 1,
                prevUrl: false,
                nextUrl: false
            }
        },

        watch: {
            dataSet() {
                this.currentPage = this.dataSet.current_page;
                this.lastPage = this.dataSet.last_page;
                this.prevUrl = this.dataSet.prev_page_url;
                this.nextUrl = this.dataSet.next_page_url;
            },


            page() {
                this.broadcast().updateUrl();
            }
        },

        computed: {
            shouldPaginate() {
                return !! this.prevUrl || !! this.nextUrl;
            },

            pages() {
                return _.range(
                    Math.max(1, this.currentPage - 2),
                    Math.min(this.lastPage, this.currentPage + 2) + 1
                );
            }
        },

        methods: {
            broadcast() {
                return this.$emit('changed', this.currentPage);
            },

            updateUrl() {
                history.pushState(null, null, '?page=' + this.currentPage);

                return this;
            },

            createURL(page) {
                return window.location.href.replace(/page=\d+/, 'page=' + page);
            }
        }
    }
</script>
