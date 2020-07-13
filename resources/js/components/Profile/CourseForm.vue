<template>
    <form @submit.prevent="$emit('submit')" @keydown="form.onKeydown($event)">
            <div class="form-group">
                <label :for="uniqueId('title')">Intitulé</label>
                <input type="text"
                       :id="uniqueId('title')"
                       v-model="form.title"
                       :class="['form-control', form.errors.has('title') ? 'is-invalid' : '' ]">

                <div v-if="form.errors.has('title')"
                     class="invalid-feedback"
                     v-text="form.errors.get('title')">
                </div>
            </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label :for="uniqueId('school')">Organisme</label>
                <input type="text"
                       :id="uniqueId('school')"
                       v-model="form.school"
                       :class="['form-control', form.errors.has('school') ? 'is-invalid' : '' ]">

                <div v-if="form.errors.has('school')"
                     class="invalid-feedback"
                     v-text="form.errors.get('school')">
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
            <div class="form-group col-sm-6">
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

            <div class="form-group col-sm-6">
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
        },

        methods: {
            uniqueId(name) {
                return name + '_' + this._uid;
            },
        }
    }
</script>
