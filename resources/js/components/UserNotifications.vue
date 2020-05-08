<template>
    <li class="nav-item dropdown">
        <button type="button" class="btn btn-link nav-link position-relative" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <svg class="bi bi-bell-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 16a2 2 0 002-2H6a2 2 0 002 2zm.995-14.901a1 1 0 10-1.99 0A5.002 5.002 0 003 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/>
            </svg>

            <!-- "New Notifications Available" bubble. -->
            <div class="rounded-circle bg-red position-absolute"
                 style="width: .5em; height: .5em; top: .5em; right: .4em;"
                 v-if="notifications.length"></div>
        </button>

        <div class="dropdown-menu dropdown-menu-right"
             aria-labelledby="navbarDropdown"
             style="width: 300px;">
            <h6 class="dropdown-header">Notifications</h6>

            <a class="dropdown-item d-flex align-items-center text-wrap px-3"
               v-for="notification in notifications"
               :key="notification.id"
               :href="notification.data.link"
               @click.prevent="markAsRead(notification)"
            >
                <img :src="notification.data.notifier.avatar_path"
                     :alt="notification.data.notifier.username"
                     class="rounded-circle cover mr-2"
                     style="width: 1.5em; height: 1.5em;">

                <span class="small" v-text="notification.data.message"></span>
            </a>

            <p class="mb-0 small text-center py-5" v-if="! notifications.length">
                Aucune notification
            </p>
        </div>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                notifications: false
            }
        },

        created() {
            axios.get('/notifications')
                .then(({data}) => {
                    this.notifications = data;
                })
        },

        methods: {
            markAsRead(notification) {
                axios.delete('/notifications/' + notification.id)
                    .then(() => {
                        document.location.replace(notification.data.link);
                    });
            }
        }
    }
</script>
