<template>
    <div class="row no-gutters pt-2 mb-4">
        <div class="col-auto flex-column pr-3">
            <img :src="post.owner.avatar_path"
                 :alt="post.owner.name"
                 class="rounded-circle cover mb-4"
                 style="width: 2.5rem; height: 2.5rem;">

            <div v-if="signedIn">
                <favorite :post="post"></favorite>
            </div>
        </div>

        <div class="col border-bottom">
            <h4 class="h6 mb-1"><a :href="'/profiles/' + post.owner.username" v-text="post.owner.name"></a></h4>

            <div class="d-flex align-items-center small mb-3">
                <p class="mb-0" v-text="ago"></p>

                <button class="btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal text-muted"
                        v-if="! isDeleted && ((authorize('owns', post) && ! threadLocked) || authorize('hasPermission', 'posts.edit'))"
                        @click="editing = true">
                    Modifier
                </button>

                <div v-if="! isDeleted && authorize('owns', post.thread)">
                    <button class="btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal text-muted"
                            v-if="isBest"
                            @click="unmarkBestPost">
                        Enlever comme meilleure réponse
                    </button>

                    <button class="btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal text-muted"
                            v-else
                            @click="markBestPost">
                        Marquer comme meilleure réponse
                    </button>
                </div>

                <button class="btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal text-muted"
                        v-if="isDeleted && showDeleted && authorize('hasPermission', 'posts.restore')"
                        @click="restore">
                    Rétablir
                </button>
            </div>

            <div v-if="isDeleted && ! showDeleted" class="text-muted d-flex align-items-center justify-content-center">
                Cette réponse a été supprimée.

                <button class="btn btn-link rounded-0 p-0 font-size-normal text-muted ml-1"
                        @click="showDeleted = true">
                    <u>La voir ?</u>
                </button>
            </div>

            <template v-else>
                <!-- Editing -->
                <form @submit.prevent="update" v-if="editing">
                    <div class="form-group">
                        <wysiwyg ref="wysiwyg" v-model="tempBody"></wysiwyg>
                    </div>

                    <div class="form-group d-flex">
                        <button class="btn btn-sm btn-outline-danger mr-2"
                                type="button"
                                v-if="! isThreadInitiator"
                                @click="destroy">
                            Supprimer
                        </button>

                        <button class="btn btn-sm btn-link ml-auto mr-2" @click="cancel">Annuler</button>
                        <button class="btn btn-sm btn-success" type="submit">Sauvegarder</button>
                    </div>
                </form>

                <!-- Viewing -->
                <div v-else-if="! editing || showDeleted">
                    <h6 class="text-success d-flex align-items-center" v-if="isBest">
                        <div class="rounded-circle text-center text-white bg-success mr-1" style="width: 1.5em; height: 1.5em;">
                            <svg class="bi bi-star-fill" style="margin-bottom: -.125em;" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </div>
                        <span>Meilleure réponse</span>
                    </h6>

                    <p class="trix-content mb-4" v-html="body"></p>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: ['post'],

        components: {Favorite},

        data() {
            return {
                editing: false,
                showDeleted: false,
                id: this.post.id,
                body: this.post.body,
                isBest: this.post.is_best,
                isThreadInitiator: this.post.is_thread_initiator,
                isDeleted: this.post.deleted_at !== null,
                tempBody: this.post.body
            }
        },

        computed: {
            ago() {
                moment.locale('fr');
                return moment(this.post.created_at).fromNow();
            },

            threadLocked() {
                return this.$parent.$parent.locked;
            }
        },

        created() {
            window.events.$on('best-post-selected', (id) => {
                this.isBest = (id === this.id);
            });
        },

        methods: {
            update() {
                axios.patch('/posts/' + this.id, {
                    body: this.tempBody
                })
                    .then(({data}) => {
                        this.body = this.tempBody; // Update value
                        this.editing = false;

                        flash('Sauvegardé !');
                    })
                    .catch(error => {
                        flash(error.response.data, 'danger');
                    });
            },

            cancel() {
                this.tempBody = this.body; // Reset to previously set value
                this.editing = false;
            },

            destroy() {
                axios.delete('/posts/' + this.id)
                    .then(({data}) => {
                        flash('Ta réponse a étée supprimée.');

                        this.$emit('deleted', this.id);
                    });
            },

            markBestPost() {
                axios.post('/posts/' + this.id + '/best')
                    .then(({data}) => {
                        window.events.$emit('best-post-selected', this.id);
                    });
            },

            unmarkBestPost() {
                axios.delete('/posts/' + this.id + '/best')
                    .then(({data}) => {
                        window.events.$emit('best-post-selected', null);
                    });
            },

            restore() {
                axios.patch('/posts/' + this.id, {
                    deleted_at: null,
                })
                    .then(({data}) => {
                        flash('La réponse a été rétablie.');

                        this.isDeleted = false;
                        this.showDeleted = false;
                    });
            },
        }
    }
</script>
