<template>
    <li class="d-flex align-items-center p-3 border-bottom">
        <svg class="bi bi-geo flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16"
             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 4a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
            <path d="M7.5 4h1v9a.5.5 0 0 1-1 0V4z"/>
            <path fill-rule="evenodd"
                  d="M6.489 12.095a.5.5 0 0 1-.383.594c-.565.123-1.003.292-1.286.472-.302.192-.32.321-.32.339 0 .013.005.085.146.21.14.124.372.26.701.382.655.246 1.593.408 2.653.408s1.998-.162 2.653-.408c.329-.123.56-.258.701-.382.14-.125.146-.197.146-.21 0-.018-.018-.147-.32-.339-.283-.18-.721-.35-1.286-.472a.5.5 0 1 1 .212-.977c.63.137 1.193.34 1.61.606.4.253.784.645.784 1.182 0 .402-.219.724-.483.958-.264.235-.618.423-1.013.57-.793.298-1.855.472-3.004.472s-2.21-.174-3.004-.471c-.395-.148-.749-.336-1.013-.571-.264-.234-.483-.556-.483-.958 0-.537.384-.929.783-1.182.418-.266.98-.47 1.611-.606a.5.5 0 0 1 .595.383z"/>
        </svg>

        <span class="sr-only">Localisation :</span>

        <span v-if="fields.location" v-text="fields.location.name"></span>

        <button v-if="canUpdate && fields.location" class="btn btn-sm py-0 ml-auto" data-toggle="modal" data-target="#editLocation">
            <span class="sr-only">Modifier</span>

            <svg class="bi bi-pencil" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                 xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M11.293 1.293a1 1 0 0 1 1.414 0l2 2a1 1 0 0 1 0 1.414l-9 9a1 1 0 0 1-.39.242l-3 1a1 1 0 0 1-1.266-1.265l1-3a1 1 0 0 1 .242-.391l9-9zM12 2l2 2-9 9-3 1 1-3 9-9z"/>
                <path fill-rule="evenodd"
                      d="M12.146 6.354l-2.5-2.5.708-.708 2.5 2.5-.707.708zM3 10v.5a.5.5 0 0 0 .5.5H4v.5a.5.5 0 0 0 .5.5H5v.5a.5.5 0 0 0 .5.5H6v-1.5a.5.5 0 0 0-.5-.5H5v-.5a.5.5 0 0 0-.5-.5H3z"/>
            </svg>
        </button>

        <button v-else-if="canUpdate" class="btn btn-sm btn-link p-0" data-toggle="modal" data-target="#editLocation">
            Ajouter ma localisation
        </button>

        <div ref="modal" class="modal fade" id="editLocation" tabindex="-1" role="dialog" style="z-index: 1060;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <h5 class="modal-title">Modifier localisation</h5>
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
                            <div class="form-group">
                                <label for="location">Localisation</label>
                                <location-input :options="locationOptions"
                                                v-model="form.location"
                                                id="location"
                                                :class="{'is-invalid': form.errors.has('location')}"
                                ></location-input>

                                <div v-if="form.errors.has('location')"
                                     class="d-block invalid-feedback"
                                     v-text="form.errors.get('location')">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-link mr-2" data-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-success">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
    import updateProfile from "../../mixins/update-profile";
    import LocationInput from "../LocationInput";

    export default {
        components: {LocationInput},
        mixins: [updateProfile],

        data() {
            return {
                locationOptions: {
                    type: 'city',
                    aroundLatLngViaIP: false,
                    templates: {
                        value: (suggestion) => {
                            return (suggestion.city || suggestion.name) + ', ' + suggestion.country;
                        }
                    },
                }
            };
        },

        methods: {
            updated() {
                $(this.$refs.modal).modal('hide');
                flash('Localisation modifiée.');
            }
        }
    }
</script>
