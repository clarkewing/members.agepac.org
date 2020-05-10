<template>
    <div class="mt-3">
        <div v-if="editing">
            <div class="form-group">
                <wysiwyg ref="wysiwyg" name="body" v-model="body" placeholder="Quelque chose à ajouter ?" required></wysiwyg>
            </div>

            <div class="form-group d-flex">
                <button class="btn btn-link ml-auto mr-2" @click="toggleEditing">Annuler</button>
                <button class="btn btn-success" @click="addReply">Publier</button>
            </div>
        </div>

        <button type="button" v-else class="btn btn-block border-placeholder p-5 d-flex align-items-center" @click="toggleEditing">
            <img :src="App.user.avatar_path"
                 :alt="App.user.name"
                 class="rounded-circle cover mr-3"
                 style="width: 2.5rem; height: 2.5rem;">

            <p class="mb-0">Participer à la discussion</p>
        </button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                body: '',
                editing: false
            }
        },

        methods: {
            addReply() {
                axios.post(location.pathname + '/replies', { body: this.body })
                    .then(({data}) => {
                        this.body = ''; // Reset for a new reply

                        flash('Ta réponse a étée publiée !');

                        this.$emit('created', data);
                    })
                    .catch(error => {
                        flash(error.response.data, 'danger');
                    });
            },

            toggleEditing() {
                this.editing = ! this.editing;

                if (this.editing) {
                    this.$nextTick(() => {
                        console.log(this.$refs.wysiwyg);
                        this.$refs.wysiwyg.$refs.editor.focus();
                    });
                }
            }
        }
    }
</script>
