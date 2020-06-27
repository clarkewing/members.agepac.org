<template>
    <div>
        <div v-for="(occupation, index) in items" :key="occupation.id">
            <occupation v-bind="occupation"
                        @destroyed="remove(index)"
            ></occupation>
        </div>
    </div>
</template>

<script>
    import collection from "../../mixins/collection";
    import Occupation from "./Occupation";

    export default {
        props: ['data'],

        mixins: [collection],
        components: {Occupation},

        data() {
            return {
                items: this.data.experience
            }
        }
    }
</script>
