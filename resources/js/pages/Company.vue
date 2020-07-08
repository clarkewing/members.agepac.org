<template>
    <div class="container text-primary bg-white mt-n4 py-4 border border-top-0 rounded-bottom">
        <div class="row mb-3">
            <div class="col-lg-10 offset-lg-1 text-right">
                <button type="button"
                        class="btn btn-info"
                        data-toggle="modal" data-target="#editCompany">
                    Modifier
                </button>
            </div>

            <div ref="modal" class="modal fade" id="editCompany" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-white">
                            <h5 class="modal-title">Modifier compagnie</h5>
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
                                          @submit="update"
                            ></company-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-lg-3 offset-md-0 offset-lg-1">
                <div class="embed-responsive embed-responsive-1by1 bg-light rounded-circle w-50 mx-auto mb-3">
                    <img v-if="showLogo"
                         class="d-block embed-responsive-item cover"
                         :src="logoSrc"
                         :alt="fields.name"
                         @error="showLogo = false">

                    <svg v-else
                         class="bi bi-building d-block position-absolute"
                         style="top: 20%; left: 20%; width: 60%; height: 60%;"
                         viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694L1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z"/>
                        <path d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z"/>
                    </svg>
                </div>

                <h1 class="h2 font-weight-bold text-center"
                    v-text="fields.name"
                ></h1>

                <h5 class="text-center"
                    v-text="fields.type"
                ></h5>

                <ul class="list-unstyled small font-weight-bold mx-4 mx-md-0 mb-5">
                    <li v-if="fields.website"
                        class="d-flex align-items-center p-3 border-bottom">
                        <svg class="bi bi-hand-index-thumb flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M6.75 1a.75.75 0 0 0-.75.75V9.5a.5.5 0 0 1-.854.354l-2.41-2.411a.517.517 0 0 0-.809.631l2.512 4.185 1.232 2.465a.5.5 0 0 0 .447.276h6.302a.5.5 0 0 0 .434-.252l1.395-2.442a2.5 2.5 0 0 0 .317-.991l.272-2.715a1 1 0 0 0-.995-1.1H13.5v1a.5.5 0 1 1-1 0V7.154a4.208 4.208 0 0 0-.2-.26c-.187-.222-.368-.383-.486-.43-.124-.05-.392-.063-.708-.039a4.844 4.844 0 0 0-.106.01V8a.5.5 0 1 1-1 0V5.986c0-.167-.073-.272-.15-.314a1.657 1.657 0 0 0-.448-.182c-.179-.035-.5-.04-.816-.027l-.086.004V8a.5.5 0 1 1-1 0V1.75A.75.75 0 0 0 6.75 1zM8.5 4.466V1.75a1.75 1.75 0 1 0-3.5 0v6.543L3.443 6.736A1.517 1.517 0 0 0 1.07 8.588l2.491 4.153 1.215 2.43A1.5 1.5 0 0 0 6.118 16h6.302a1.5 1.5 0 0 0 1.302-.756l1.395-2.441a3.5 3.5 0 0 0 .444-1.389l.272-2.715a2 2 0 0 0-1.99-2.199h-.582a5.114 5.114 0 0 0-.195-.248c-.191-.229-.51-.568-.88-.716-.364-.146-.846-.132-1.158-.108l-.132.012a1.26 1.26 0 0 0-.56-.642 2.634 2.634 0 0 0-.738-.288c-.31-.062-.739-.058-1.05-.046l-.048.002zm2.094 2.025z"/>
                        </svg>
                        <span class="sr-only">Site web :</span>
                        <span v-text="fields.website"></span>
                    </li>
                </ul>

                <div class="text-center small">
                    <a v-if="showLogo"
                       href="https://clearbit.com"
                       target="_blank">
                        Logo fourni par Clearbit
                    </a>
                </div>
            </div>

            <div class="col-md-8 col-lg-7">
                <div v-if="fields.description">
                    <h2 class="h3 font-weight-bold mb-3">Description</h2>
                    <p class="mb-5" v-text="fields.description"></p>
                </div>

                <div v-if="fields.operations">
                    <h2 class="h3 font-weight-bold mb-3">Opérations</h2>
                    <p class="mb-5" v-text="fields.operations"></p>
                </div>

                <div v-if="fields.conditions">
                    <h2 class="h3 font-weight-bold mb-3">Conditions</h2>
                    <p class="mb-5" v-text="fields.conditions"></p>
                </div>

                <div v-if="fields.remarks">
                    <h2 class="h3 font-weight-bold mb-3">Remarques</h2>
                    <p class="mb-5" v-text="fields.remarks"></p>
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
                resourceId: this.data.slug,

                form: new Form(_.pick(this.data, [
                    'name',
                    'type_code',
                    'website',
                    'description',
                    'operations',
                    'conditions',
                    'remarks',
                ])),

                showLogo: true,
            }
        },

        computed: {
            logoSrc() {
                if (! this.fields.website) {
                    this.showLogo = false;

                    return null;
                }

                return 'https://logo.clearbit.com/'
                    + new URL(this.fields.website).hostname;
            },

            type() {
                return App.companyTypes[this.type_code];
            },
        },

        methods: {
            updated() {
                this.resourceId = this.fields.slug;

                history.pushState(this.fields, null, this.fields.slug);

                $(this.$refs.modal).modal('hide');

                flash('Compagnie modifiée.');
            },
        },
    }
</script>
