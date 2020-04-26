<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
                <wysiwyg id="body" name="body" v-model="body" placeholder="Quelque chose à ajouter ?" required></wysiwyg>
            </div>
            <button class="btn btn-primary"
                @click="addReply">Publier</button>
        </div>
    </div>
</template>

<script>
    import Tribute from 'tributejs';
    export default {
        data() {
            return {
                body: ''
            }
        },

        mounted() {
            var tribute = new Tribute({
                trigger: '@',

                // column to search against in the object (accepts function or string)
                lookup: 'name',

                // column that contains the content to insert by default
                fillAttr: 'name',

                values: (query, cb) => {
                    this.remoteSearch(query, users => cb(users));
                }
            });

            tribute.attach(document.getElementById('body'));
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
            remoteSearch(query, cb) {
                axios.get('/api/users', {
                    params: {
                        name: query
                    }
                })
                .then(function (response) {
                    cb(response.data);
                })
                .catch(function (error) {
                    cb([]);
                });
            }
        }
    }
</script>
