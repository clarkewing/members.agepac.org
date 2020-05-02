<template>
    <a :href="route" @click.prevent="logout">
        <slot/>
    </a>
</template>

<script>
    export default {
        props: {
            route: {
                type: String,
                default: '/logout'
            }
        },

        methods: {
            logout() {
                axios.post(this.route)
                    .then(() => window.location.href = '/')
                    .catch(() => window.location.reload());
            }
        }
    }
</script>
