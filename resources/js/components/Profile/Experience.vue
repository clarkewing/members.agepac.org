<template>
    <div>
        <div class="d-flex">
            <h2 class="h3 font-weight-bold mb-3">Expérience Professionelle</h2>

            <button v-if="items.length"
                    class="btn btn-sm ml-auto align-self-start"
                    data-toggle="modal" data-target="#createOccupation">
                <span class="sr-only">Ajouter</span>

                <svg class="bi bi-plus-circle" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M8 3.5a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5H4a.5.5 0 0 1 0-1h3.5V4a.5.5 0 0 1 .5-.5z"/>
                    <path fill-rule="evenodd" d="M7.5 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0V8z"/>
                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                </svg>
            </button>
        </div>

        <div ref="modal" class="modal fade" id="createOccupation" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <h5 class="modal-title">Ajouter un emploi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg class="bi bi-x" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M11.854 4.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708-.708l7-7a.5.5 0 0 1 .708 0z"/>
                                <path fill-rule="evenodd"
                                      d="M4.146 4.146a.5.5 0 0 0 0 .708l7 7a.5.5 0 0 0 .708-.708l-7-7a.5.5 0 0 0-.708 0z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="modal-body">
                        <occupation-form v-bind:form.sync="form"
                                         @submit="create"
                        ></occupation-form>
                    </div>
                </div>
            </div>
        </div>

        <div v-for="occupation in orderedItems" :key="occupation.id">
            <occupation v-bind="occupation"
                        @destroyed="removeId(occupation.id)"
            ></occupation>
        </div>

        <button v-if="! items.length"
                class="btn btn-sm btn-link p-0 mb-5"
                data-toggle="modal" data-target="#createOccupation">
            Ajouter un emploi
        </button>
    </div>
</template>

<script>
    import collection from "../../mixins/collection";
    import {Form} from 'vform';
    import Occupation from "./Occupation";
    import OccupationForm from "./OccupationForm";
    import updateProfile from "../../mixins/update-profile";

    export default {
        props: ['data'],

        mixins: [collection, updateProfile],
        components: {Occupation, OccupationForm},

        data() {
            return {
                endpoint: '/occupations',
                resourceId: this.id,

                form: new Form({
                    position: null,
                    aircraft_id: null,
                    company: null,
                    location: null,
                    status_code: null,
                    description: null,
                    start_date: null,
                    end_date: null,
                }),
                fields: null,

                items: this.data.experience,
            }
        },

        computed: {
            orderedItems() {
                return _.orderBy(this.items, ['start_date', 'is_primary'], ['desc', 'desc']);
            }
        },

        methods: {
            created(data) {
                this.add(data);

                $(this.$refs.modal).modal('hide');
                flash('Emploi ajouté.');
            }
        }
    }
</script>
