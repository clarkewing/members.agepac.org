<template>
    <div class="d-flex align-items-center py-3 py-md-4 border-bottom position-relative">
        <div class="embed-responsive embed-responsive-1by1 bg-light rounded-circle flex-shrink-0 mr-3"
             style="width: 3.5rem;">
            <img class="d-block embed-responsive-item cover"
                 :src="profile_photo_url"
                 :alt="name"
                 @error="showLogo = false">
        </div>

        <div class="flex-grow-1">
            <a class="stretched-link h5 text-primary flex-grow-1 mr-3"
               :href="path"
               v-text="name"
            ></a>

            <h6 class="small text-muted">{{ class_course }} {{ class_year }}</h6>
        </div>

        <h6 v-if="mentorship_tags.length"
            class="small font-weight-bold flex-shrink-0 rounded-pill p-2 px-md-3"
            style="background: rgb(111,170,243);"
            v-text="mentorship_tags[0]">
        </h6>

        <h6 v-if="mentorship_tags.length > 1"
            class="small font-weight-bold flex-shrink-0 rounded-pill bg-gray-200 p-2 px-md-3 ml-1">
            + {{ mentorship_tags.length - 1 }}
        </h6>
    </div>
</template>

<script>
    export default {
        props: [
            'id',
            'first_name',
            'last_name',
            'username',
            'class_course',
            'class_year',
            'profile_photo_url',
            'mentorship_tags',
        ],

        computed: {
            name() {
                return this.first_name + ' ' + this.last_name;
            },

            path() {
                return '/profiles/' + this.username;
            }
        },
    }
</script>
