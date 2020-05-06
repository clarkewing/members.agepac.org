<template>
    <button :class="[classes, 'btn btn-link rounded-0 border-left p-0 pl-2 ml-2 font-size-normal']" @click="toggleSubscription">
        <span v-if="isActive">
            <svg class="bi bi-check" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z" clip-rule="evenodd"/>
            </svg>
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
                    ? 'text-primary font-weight-bolder'
                    : 'text-muted';
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
