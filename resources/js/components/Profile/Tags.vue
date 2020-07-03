<template>
    <div>
        <div class="d-flex justify-content-center flex-wrap mb-3">
            <h6 v-for="(tag, index) in tags"
                v-text="tag"
                class="small font-weight-bold flex-shrink-0 rounded-pill p-2 px-md-3"
                :class="{'mr-2': index !== tags.length - 1 || canUpdate}"
                style="background: rgb(111,170,243);"
            ></h6>

            <button v-if="canUpdate"
                    data-toggle="modal" data-target="#editMentorshipTags"
                    class="btn btn-sm font-weight-bold flex-shrink-0 bg-light rounded-pill p-2 px-md-3 mb-2"
                    style="font-size: .72rem; line-height: 1.2;"
            >
                <svg class="bi bi-plus mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M8 3.5a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5H4a.5.5 0 0 1 0-1h3.5V4a.5.5 0 0 1 .5-.5z"/>
                    <path fill-rule="evenodd" d="M7.5 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0V8z"/>
                </svg>
                Domaine de mentorat
            </button>
        </div>

        <div ref="modal" class="modal fade" id="editMentorshipTags" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <h5 class="modal-title">Modifier mes domaines de mentorat</h5>
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
                        <p>
                            En listant un domaine, tu acceptes d'être contacté par d'autres membres qui aurient des
                            questions concernant les domaines que tu auras sélectionnés.
                        </p>

                        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
                            <div class="form-group">
                                <label for="mentorship_tags">Domaines de mentorat</label>
                                <tags-select :value="tags"
                                             @input="form.mentorship_tags = $event"
                                             type="mentorship"
                                             id="mentorship_tags"
                                ></tags-select>


                                <div v-if="form.errors.has('mentorship_tags')"
                                     class="invalid-feedback"
                                     v-text="form.errors.get('mentorship_tags')">
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
    </div>
</template>

<script>
    import updateProfile from "../../mixins/update-profile";
    import TagsSelect from "../TagsSelect";

    export default {
        mixins: [updateProfile],
        components: {TagsSelect},

        computed: {
            tags() {
                return _.map(this.fields.mentorship_tags, (value) => value.name.fr);
            }
        },

        methods: {
            updated() {
                $(this.$refs.modal).modal('hide');
                flash('Domaines de mentorat modifiés.');
            }
        }
    }
</script>
