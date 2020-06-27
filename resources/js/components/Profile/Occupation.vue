<template>
    <div class="mb-5">
        <div class="d-flex align-items-start">
            <h5 class="align-self-end flex-grow-1"
                v-text="fields.title"
            ></h5>

            <h6 class="small font-weight-bold flex-shrink-0 rounded-pill p-2 px-md-3"
                :style="{background: statusColor}"
                v-text="fields.status"
            ></h6>

            <button class="btn btn-sm ml-2" data-toggle="modal" :data-target="'#' + uniqueId('editOccupation')">
                <span class="sr-only">Modifier</span>

                <svg class="bi bi-pencil" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M11.293 1.293a1 1 0 0 1 1.414 0l2 2a1 1 0 0 1 0 1.414l-9 9a1 1 0 0 1-.39.242l-3 1a1 1 0 0 1-1.266-1.265l1-3a1 1 0 0 1 .242-.391l9-9zM12 2l2 2-9 9-3 1 1-3 9-9z"/>
                    <path fill-rule="evenodd"
                          d="M12.146 6.354l-2.5-2.5.708-.708 2.5 2.5-.707.708zM3 10v.5a.5.5 0 0 0 .5.5H4v.5a.5.5 0 0 0 .5.5H5v.5a.5.5 0 0 0 .5.5H6v-1.5a.5.5 0 0 0-.5-.5H5v-.5a.5.5 0 0 0-.5-.5H3z"/>
                </svg>
            </button>

            <div ref="modal" class="modal fade" :id="uniqueId('editOccupation')" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-white">
                            <h5 class="modal-title">Modifier emploi</h5>
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
                            <form @submit.prevent="update" @keydown="form.onKeydown($event)">
                                <div class="form-row">
                                    <div class="form-group col-md-8">
                                        <label :for="uniqueId('position')">Intitulé du poste</label>
                                        <div class="input-group">
                                            <input type="text"
                                                   :id="uniqueId('position')"
                                                   v-model="form.position"
                                                   :class="['form-control', form.errors.has('position') ? 'is-invalid' : '' ]">

                                            <div class="input-group-append">
                                                <div class="input-group-text form-check form-check-inline mr-0">
                                                    <input type="checkbox"
                                                           :id="uniqueId('is_pilot')"
                                                           v-model="isPilot"
                                                           class="form-check-input">
                                                    <label class="form-check-label" :for="uniqueId('is_pilot')">
                                                        Emploi de pilote
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="form.errors.has('position')"
                                             class="d-block invalid-feedback"
                                             v-text="form.errors.get('position')">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label :for="uniqueId('aircraft_id')">Avion exploité</label>
                                        <select :id="uniqueId('aircraft_id')"
                                                :disabled="! isPilot"
                                                v-model="form.aircraft_id"
                                                :class="['form-control', form.errors.has('aircraft_id') ? 'is-invalid' : '' ]">
                                            <option value="" disabled></option>
                                            <option v-for="aircraft of App.aircrafts"
                                                    :value="aircraft.id"
                                                    v-text="aircraft.name"
                                            ></option>
                                        </select>

                                        <div v-if="form.errors.has('aircraft_id')"
                                             class="invalid-feedback"
                                             v-text="form.errors.get('aircraft_id')">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label :for="uniqueId('company')">Employeur</label>
                                        <input type="text"
                                               :id="uniqueId('company')"
                                               v-model="form.company"
                                               :class="['form-control', form.errors.has('company') ? 'is-invalid' : '' ]">

                                        <div v-if="form.errors.has('company')"
                                             class="invalid-feedback"
                                             v-text="form.errors.get('company')">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label :for="uniqueId('location')">Localisation</label>
                                        <location-input :options="locationOptions"
                                                        :id="uniqueId('location')"
                                                        v-model="form.location"
                                                        :class="{'is-invalid': form.errors.has('location')}"
                                        ></location-input>

                                        <div v-if="form.errors.has('location')"
                                             class="d-block invalid-feedback"
                                             v-text="form.errors.get('location')">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-sm-6 col-md">
                                        <label :for="uniqueId('start_date')">Date de début</label>
                                        <b-form-datepicker hide-header reset-button locale="fr" start-weekday="1"
                                                           :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                                           :id="uniqueId('start_date')"
                                                           v-model="form.start_date"
                                                           :max="form.end_date"
                                                           :class="[form.errors.has('start_date') ? 'is-invalid' : '' ]"
                                        ></b-form-datepicker>

                                        <div v-if="form.errors.has('start_date')"
                                             class="invalid-feedback"
                                             v-text="form.errors.get('start_date')">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6 col-md">
                                        <label :for="uniqueId('end_date')">Date de fin</label>
                                        <b-form-datepicker hide-header reset-button locale="fr" start-weekday="1"
                                                           :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                                           :id="uniqueId('end_date')"
                                                           v-model="form.end_date"
                                                           :min="form.start_date"
                                                           :max="new Date()"
                                                           :class="[form.errors.has('end_date') ? 'is-invalid' : '' ]"
                                        ></b-form-datepicker>

                                        <div v-if="form.errors.has('end_date')"
                                             class="invalid-feedback"
                                             v-text="form.errors.get('end_date')">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-5">
                                        <label :for="uniqueId('status_code')">Statut</label>
                                        <select :id="uniqueId('status_code')"
                                                v-model="form.status_code"
                                                :class="['form-control', form.errors.has('status_code') ? 'is-invalid' : '' ]">
                                            <option value="" disabled></option>
                                            <option v-for="(status, statusCode) of App.occupationStatuses"
                                                    :value="statusCode"
                                                    v-text="status"
                                            ></option>
                                        </select>

                                        <div v-if="form.errors.has('status_code')"
                                             class="invalid-feedback"
                                             v-text="form.errors.get('status_code')">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label :for="uniqueId('escription')">Description</label>
                                    <textarea :id="uniqueId('escription')"
                                              rows="3"
                                              v-model="form.description"
                                              :class="['form-control', form.errors.has('description') ? 'is-invalid' : '' ]"
                                    ></textarea>

                                    <div v-if="form.errors.has('description')"
                                         class="invalid-feedback"
                                         v-text="form.errors.get('description')">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-danger mr-auto"
                                            @click="destroy">
                                        Supprimer
                                    </button>

                                    <button type="button" class="btn btn-link mr-2" data-dismiss="modal">
                                        Annuler
                                    </button>

                                    <button type="submit" class="btn btn-success">
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap">
            <h6 class="flex-shrink-0 small text-muted mr-4">
                <svg class="bi bi-building mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                     fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M15.285.089A.5.5 0 0 1 15.5.5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5H1a.5.5 0 0 1-.5-.5v-6a.5.5 0 0 1 .418-.493l5.582-.93V3.5a.5.5 0 0 1 .324-.468l8-3a.5.5 0 0 1 .46.057zM7.5 3.846V8.5a.5.5 0 0 1-.418.493l-5.582.93V15h8v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.222l-7 2.624z"/>
                    <path fill-rule="evenodd" d="M6.5 15.5v-7h1v7h-1z"/>
                    <path
                        d="M2.5 11h1v1h-1v-1zm2 0h1v1h-1v-1zm-2 2h1v1h-1v-1zm2 0h1v1h-1v-1zm6-10h1v1h-1V3zm2 0h1v1h-1V3zm-4 2h1v1h-1V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm-2 2h1v1h-1V7zm2 0h1v1h-1V7zm-4 0h1v1h-1V7zm0 2h1v1h-1V9zm2 0h1v1h-1V9zm2 0h1v1h-1V9zm-4 2h1v1h-1v-1zm2 0h1v1h-1v-1zm2 0h1v1h-1v-1z"/>
                </svg>
                <span class="sr-only">Entreprise :</span>

                <span v-text="fields.company"></span>
            </h6>

            <h6 class="flex-shrink-0 flex-grow-1 small text-muted mr-4">
                <svg class="bi bi-geo mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                     fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 4a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                    <path d="M7.5 4h1v9a.5.5 0 0 1-1 0V4z"/>
                    <path fill-rule="evenodd"
                          d="M6.489 12.095a.5.5 0 0 1-.383.594c-.565.123-1.003.292-1.286.472-.302.192-.32.321-.32.339 0 .013.005.085.146.21.14.124.372.26.701.382.655.246 1.593.408 2.653.408s1.998-.162 2.653-.408c.329-.123.56-.258.701-.382.14-.125.146-.197.146-.21 0-.018-.018-.147-.32-.339-.283-.18-.721-.35-1.286-.472a.5.5 0 1 1 .212-.977c.63.137 1.193.34 1.61.606.4.253.784.645.784 1.182 0 .402-.219.724-.483.958-.264.235-.618.423-1.013.57-.793.298-1.855.472-3.004.472s-2.21-.174-3.004-.471c-.395-.148-.749-.336-1.013-.571-.264-.234-.483-.556-.483-.958 0-.537.384-.929.783-1.182.418-.266.98-.47 1.611-.606a.5.5 0 0 1 .595.383z"/>
                </svg>
                <span class="sr-only">Lieu :</span>

                <span v-text="fields.location.name"></span>
            </h6>

            <h6 class="flex-shrink-0 small text-muted">
                <svg class="bi bi-calendar mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                     fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1zm1-3a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2z"/>
                    <path fill-rule="evenodd"
                          d="M3.5 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 .5-.5zm9 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 .5-.5z"/>
                </svg>
                <span class="sr-only">Dates :</span>

                <span>
                    {{ startDate }} - {{ endDate }}
                </span>
            </h6>
        </div>

        <p v-if="fields.description" v-text="fields.description"></p>
    </div>
</template>

<script>
    import {BFormDatepicker} from 'bootstrap-vue';
    import 'bootstrap-vue/dist/bootstrap-vue.css';
    import LocationInput from "../LocationInput";
    import {Form} from 'vform';
    import moment from 'moment';
    import updateProfile from "../../mixins/update-profile";

    export default {
        mixins: [updateProfile],
        components: {BFormDatepicker, LocationInput},

        props: [
            'id',
            'title',
            'position',
            'is_pilot',
            'aircraft_id',
            'company',
            'location',
            'status',
            'status_code',
            'description',
            'start_date',
            'end_date',
            'is_primary',
        ],

        data() {
            return {
                endpoint: '/occupations',
                resourceId: this.id,

                form: new Form(_.pick(this.$props, [
                    'position',
                    'aircraft_id',
                    'company',
                    'location',
                    'status_code',
                    'description',
                    'start_date',
                    'end_date',
                    'is_primary',
                ])),

                fields: _.omit(this.$props, []), // _.omit produces an independent copy

                isPilot: this.is_pilot,

                locationOptions: {
                    templates: {
                        value: (suggestion) => {
                            if (suggestion.type === 'city' || suggestion.type === 'address') {
                                return (suggestion.city || suggestion.name) + ', ' + suggestion.country;
                            }

                            if (suggestion.type === 'airport') {
                                return suggestion.name + ', ' + suggestion.administrative + ', ' + suggestion.country;
                            }

                            return suggestion.name + ', ' + suggestion.city + ', ' + suggestion.administrative + ', ' + suggestion.country;
                        }
                    },
                }
            };
        },

        computed: {
            statusColor() {
                switch (this.fields.status_code) {
                    case 1:
                        return '#9AE6B4';

                    case 2:
                        return '#FBD38D';

                    case 3:
                        return '#D6BCFA';

                    case 4:
                        return '#A3BFFA';
                }
            },

            startDate() {
                return moment(this.fields.start_date).format('LL');
            },

            endDate() {
                if (this.end_date) {
                    return moment(this.fields.end_date).format('LL');
                }
            }
        },

        watch: {
            isPilot(value) {
                // Null aircraft_id if user unchecks isPilot.
                if (!value) {
                    this.form.aircraft_id = null;
                }
            }
        },

        methods: {
            updated() {
                $(this.$refs.modal).modal('hide');
                flash('Emploi modifié.');
            },

            deleted() {
                $(this.$refs.modal).modal('hide');

                flash('Emploi supprimé.');

                this.$emit('destroyed', this.fields.id);
            },

            uniqueId(name) {
                return name + this.fields.id;
            },
        },

        created() {
            moment.locale('fr');
        }
    }
</script>
