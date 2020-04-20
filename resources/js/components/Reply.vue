<template>
    <div :id="'reply-'+id" class="card mb-4" :class="isBest ? 'border-success' : ''">
        <div class="card-header d-flex align-items-center" :class="isBest ? 'text-white bg-success' : ''">
            <h6 class="mb-0 flex-grow-1">
                <a :href="'/profiles/'+reply.owner.name"
                    v-text="reply.owner.name">
                </a> a dit <span v-text="ago"></span>...
            </h6>
            <div v-if="signedIn">
                <favorite :reply="reply"></favorite>
            </div>
        </div>
        <div class="card-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <div class="form-group">
                        <wysiwyg v-model="body"></wysiwyg>
                    </div>
                    <button class="btn btn-sm btn-primary" type="submit">Sauvegarder</button>
                    <button class="btn btn-sm btn-link" @click="editing = false" type="button">Annuler</button>
                </form>
            </div>

            <div v-else v-html="body"></div>
        </div>

        <div class="card-footer d-flex" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
            <div v-if="authorize('owns', reply)">
                <button class="btn btn-sm btn-outline-secondary mr-2" @click="editing = true">Modifier</button>
                <button class="btn btn-sm btn-outline-danger" @click="destroy">Supprimer</button>
            </div>
            <button class="btn btn-sm btn-outline-success ml-auto" @click="markBestReply" v-if="authorize('owns', reply.thread) && !isBest">Marquer comme meilleure réponse</button>
        </div>
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: ['reply'],

        components: { Favorite },

        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                isBest: this.reply.is_best,
            }
        },

        computed: {
            ago() {
                moment.locale('fr');
                return moment(this.reply.created_at).fromNow();
            }
        },

        created () {
            window.events.$on('best-reply-selected', (id) => {
                this.isBest = (id === this.id);
            });
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.id, {
                    body: this.body
                })
                .then(({data}) => {
                    this.editing = false;

                    flash('Sauvegardé !');
                })
                .catch(error => {
                    flash(error.response.data, 'danger');
                });
            },

            destroy() {
                axios.delete('/replies/' + this.id)
                    .then(({data}) => {
                        this.$emit('deleted', this.id);
                    });
            },

            markBestReply() {
                axios.post('/replies/' + this.id + '/best')
                    .then(({data}) => {
                        window.events.$emit('best-reply-selected', this.id);
                    });
            }
        }
    }
</script>
