<template>
    <div>
        <button type="button"
                class="btn btn-success"
                data-toggle="modal" data-target="#createCompany">
            <svg class="bi bi-file-earmark-plus" width="1em" height="1em" viewBox="0 0 16 16"
                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M9 1H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h5v-1H4a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h5v2.5A1.5 1.5 0 0 0 10.5 6H13v2h1V6L9 1z"/>
                <path fill-rule="evenodd"
                      d="M13.5 10a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1H13v-1.5a.5.5 0 0 1 .5-.5z"/>
                <path fill-rule="evenodd"
                      d="M13 12.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0v-2z"/>
            </svg>
            <span class="d-none d-md-inline ml-1">Nouvelle compagnie</span>
        </button>

        <div ref="modal" class="modal fade" id="createCompany" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <h5 class="modal-title">Nouvelle compagnie</h5>
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
                        <company-form v-bind:form.sync="form"
                                      @submit="create"
                        ></company-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CompanyForm from "../components/CompanyForm";
    import {Form} from "vform";
    import crudResource from "../mixins/crud-resource";

    export default {
        mixins: [crudResource],
        components: {CompanyForm},

        data() {
            return {
                endpoint: '/companies',

                form: new Form({
                    'name': null,
                    'type_code': null,
                    'website': null,
                    'description': null,
                    'operations': null,
                    'conditions': null,
                    'remarks': null,
                }),

                showLogo: true,
            }
        },

        methods: {
            created() {
                $(this.$refs.modal).modal('hide');

                flash('Compagnie ajoutée.');
            },
        },
    }
</script>
