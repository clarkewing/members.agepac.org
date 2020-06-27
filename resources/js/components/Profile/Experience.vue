<template>
    <div>
        <occupation v-for="occupation in experience"
                    :key="occupation.id"
                    v-bind="occupation"
        ></occupation>
    </div>
</template>

<script>
    import Occupation from "./Occupation";

    export default {
        props: ['data'],

        components: {Occupation},

        data() {
            return {
                experience: this.data.experience
            }
        }
    }
</script>
