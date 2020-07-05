<template>
    <multiselect v-model="company"
                 :options="suggestions"
                 :loading="isLoading"
                 :multiple="false"
                 taggable
                 track-by="id"
                 label="name"
                 :allow-empty="false"
                 :internal-search="false"
                 @search-change="getSuggestions"
                 @tag="addCompany"
    ></multiselect>
</template>

<script>
    import Multiselect from 'vue-multiselect';

    export default {
        components: {Multiselect},

        props: {
            value: Object,
        },

        data() {
            return {
                company: this.value,
                suggestions: [],

                endpoint: '/companies',
                isLoading: false,
            }
        },

        watch: {
            company() {
                this.$emit('input', this.company);
            }
        },

        methods: {
            addCompany(newCompany) {
                const newCompanyObject = {
                    id: null,
                    name: newCompany,
                };

                this.company = newCompanyObject;
                this.suggestions.push(newCompanyObject);
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
                }).then(({data: {data}}) => {
                    if (data.length) {
                        this.suggestions = _.map(data, (company) => _.pick(company, ['id', 'name']));
                    } else {
                        this.suggestions = [];
                    }

                    this.isLoading = false;
                });
            }, 300),
        }
    }
</script>
