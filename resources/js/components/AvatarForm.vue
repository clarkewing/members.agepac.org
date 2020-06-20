<template>
    <div :class="['embed-responsive embed-responsive-1by1', classes]">
        <img :src="avatar" class="d-block embed-responsive-item cover">

        <label class="embedded-avatar-upload-btn d-flex align-items-center justify-content-center" v-if="canUpdate">
            <svg class="bi bi-upload" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                 xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M.5 8a.5.5 0 0 1 .5.5V12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8.5a.5.5 0 0 1 1 0V12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8.5A.5.5 0 0 1 .5 8zM5 4.854a.5.5 0 0 0 .707 0L8 2.56l2.293 2.293A.5.5 0 1 0 11 4.146L8.354 1.5a.5.5 0 0 0-.708 0L5 4.146a.5.5 0 0 0 0 .708z"/>
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0v-8A.5.5 0 0 1 8 2z"/>
            </svg>

            <image-upload name="avatar" @loaded="onLoad"></image-upload>
        </label>
    </div>
</template>

<script>
    import ImageUpload from './ImageUpload.vue';

    export default {
        props: ['user', 'classes'],

        components: {ImageUpload},

        data() {
            return {
                avatar: this.user.avatar_path
            }
        },

        computed: {
            canUpdate() {
                return this.authorize(user => user.id === this.user.id);
            }
        },

        methods: {
            onLoad(avatar) {
                this.avatar = avatar.src;
                this.persist(avatar.file);
            },

            persist(avatar) {
                let data = new FormData();

                data.append('avatar', avatar);

                axios.post(`/api/users/${this.user.username}/avatar`, data)
                    .then(() => flash('Photo de profil chargée !'))
            }
        }
    }
</script>
