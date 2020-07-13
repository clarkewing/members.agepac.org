<template>
    <div class="d-flex align-items-center py-3 py-md-4 border-bottom position-relative">
        <div class="embed-responsive embed-responsive-1by1 bg-light rounded-circle flex-shrink-0 mr-3"
             style="width: 3.5rem;">
            <img v-if="showLogo"
                 class="d-block embed-responsive-item cover"
                 :src="logoSrc"
                 :alt="name"
                 @error="showLogo = false">

            <svg v-else
                 class="bi bi-building d-block position-absolute"
                 style="top: 20%; left: 20%; width: 60%; height: 60%;"
                 viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694L1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z"/>
                <path
                    d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z"/>
            </svg>
        </div>

        <a class="stretched-link h5 text-primary flex-grow-1 mr-3"
           :href="path"
           v-text="name"
        ></a>

        <h6 class="small font-weight-bold flex-shrink-0 rounded-pill p-2 px-md-3"
            style="background: #BEE3F8;"
            v-text="type"
        ></h6>
    </div>
</template>

<script>
    export default {
        props: [
            'name',
            'slug',
            'type',
            'website',
        ],

        data() {
            return {
                showLogo: true,
            }
        },

        computed: {
            logoSrc() {
                if (!this.website) {
                    this.showLogo = false;

                    return null;
                }

                return 'https://logo.clearbit.com/'
                    + new URL(this.website).hostname;
            },

            path() {
                return '/companies/' + this.slug;
            }
        },
    }
</script>
