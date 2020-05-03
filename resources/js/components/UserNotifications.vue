<template>
    <li class="nav-item dropdown" v-if="notifications.length">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" style="fill: currentColor;"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
        </a>

        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li v-for="notification in notifications" :key="notification.id">
                <a class="dropdown-item"
                    :href="notification.data.link"
                    v-text="notification.data.message"
                    @click.prevent="markAsRead(notification)"
                ></a>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                endpoint: '/notifications',
                notifications: false
            }
        },

        created() {
            this.fetchNotifications();
        },

        methods: {
            fetchNotifications() {
                axios.get(this.endpoint)
                    .then(({data}) => this.notifications = data);
            },

            markAsRead(notification) {
                axios.delete(this.endpoint + '/' + notification.id)
                    .then(({data}) => {
                        this.fetchNotifications();

                        document.location.replace(data.link);
                    });
            }
        }
    }
</script>
