<template>
    <multiselect v-model="tags"
                 :options="suggestions"
                 :loading="isLoading"
                 :multiple="true"
                 taggable
                 hide-selected
                 :internal-search="false"
                 @search-change="getSuggestions"
                 @tag="addTag"
    ></multiselect>
</template>

<script>
    import Multiselect from 'vue-multiselect';

    export default {
        components: {Multiselect},

        props: {
            value: Array,
            type: String,
            lang: {
                type: String,
                default: 'fr',
            },
        },

        data() {
            return {
                tags: this.value,
                suggestions: [],

                endpoint: '/api/tags' + (this.type ? '/' + this.type : ''),
                isLoading: false,
            }
        },

        watch: {
            tags() {
                this.$emit('input', this.tags);
            }
        },

        methods: {
            addTag(newTag) {
                this.tags.push(newTag);
            },

            getSuggestions: _.debounce(function (query) {
                if (! query.length) {
                    return;
                }

                this.isLoading = true;

                axios.get(this.endpoint, {
                    params: {
                        query: query,
                    }
                }).then(({data}) => {
                    if (data.length) {
                        this.suggestions = _.map(data, (value) => value.name[this.lang]);
                    } else {
                        this.suggestions = [];
                    }

                    this.isLoading = false;
                });
            }, 300),
        }
    }
</script>
