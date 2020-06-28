<template>
    <form @submit.prevent="$emit('submit')" @keydown="form.onKeydown($event)">
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
            <label :for="uniqueId('description')">Description</label>
            <textarea :id="uniqueId('description')"
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
                    v-if="showDelete"
                    @click="$emit('destroy')">
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
</template>

<script>
    import {BFormDatepicker} from 'bootstrap-vue';
    import 'bootstrap-vue/dist/bootstrap-vue.css';
    import LocationInput from "../LocationInput";

    export default {
        components: {BFormDatepicker, LocationInput},

        props: {
            form: {
                type: Object,
                required: true,
            },
            showDelete: Boolean,
        },

        data() {
            return {
                isPilot: !! this.form.aircraft_id,

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
                },
            };
        },

        watch: {
            form(value) {
                this.$emit('update:form', value);
            },

            isPilot(value) {
                // Null aircraft_id if user unchecks isPilot.
                if (!value) {
                    this.form.aircraft_id = null;
                }
            },
        },

        methods: {
            uniqueId(name) {
                return name + '_' + this._uid;
            },
        }
    }
</script>
