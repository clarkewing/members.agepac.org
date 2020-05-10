<template>
    <div>
        <button type="button" class="btn d-block shadow-none p-0 mx-auto"
                :class="[active ? 'text-info' : 'text-gray-400']"
                @click="toggle">
            <svg class="bi bi-heart-fill"
                 width="1.125rem" height="1.125rem"
                 viewBox="0 0 16 16"
                 fill="currentColor"
                 xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"
                      clip-rule="evenodd"/>
            </svg>
        </button>

        <p class="text-center mb-0"
           :class="[active ? 'text-info' : 'text-gray-400']"
           v-text="count"></p>
    </div>
</template>

<script>
    export default {
        props: ['post'],

        data() {
            return {
                count: this.post.favorites_count || 0,
                active: this.post.is_favorited
            }
        },

        computed: {
            endpoint() {
                return '/posts/' + this.post.id + '/favorites';
            }
        },

        methods: {
            toggle() {
                return this.active ? this.destroy() : this.create();
            },

            create() {
                axios.post(this.endpoint);

                this.active = true;
                this.count++;
            },

            destroy() {
                axios.delete(this.endpoint);

                this.active = false;
                this.count--;
            }
        }
    }
</script>
