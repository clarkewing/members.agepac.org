<template>
    <button class="btn" :class="classes" @click="toggleSubscription">
        <span v-if="isActive">
            <svg xmlns="http://www.w3.org/2000/svg" height="22" viewBox="0 0 24 24" width="22" fill="#FFF"><path d="M0 0h24v24H0z" fill="none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            Abonné
        </span>

        <span v-else>
            S'abonner
        </span>
    </button>
</template>

<script>
    export default {
        props: ['active'],

        data() {
            return {
                isActive: this.active
            }
        },

        computed: {
            classes() {
                return this.isActive
                    ? 'btn-primary'
                    : 'btn-outline-primary';
            }
        },

        methods: {
            toggleSubscription() {
                axios[
                    (this.isActive ? 'delete' : 'post')
                ](location.pathname + '/subscriptions')
                    .then(() => {
                        this.isActive = !this.isActive;

                        flash(this.isActive
                            ? 'Tu as été abonné !'
                            : 'Tu as été désabonné.');
                    });
            }
        }
    }
</script>
