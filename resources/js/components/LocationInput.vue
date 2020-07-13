<template>
    <input type="text"
           ref="input"
           class="form-control">
</template>

<script>
    import places from 'places.js';

    export default {
        props: {
            value: Object,
            options: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },

        data() {
            return {
                places: null,

                defaultOptions: {
                    language: 'fr',
                }
            };
        },

        methods: {
            setValue(location) {
                this.$emit('input', location);
            },

            buildLocation(place) {
                return {
                    type: place.type,
                    name: place.value,
                    street_line_1: place.type === 'address' ? place.name : null,
                    street_line_2: null,
                    municipality: place.city || place.name,
                    administrative_area: place.administrative,
                    sub_administrative_area: place.county || place.suburb || null,
                    postal_code: place.postcode || null,
                    country: place.country,
                    country_code: place.countryCode.toUpperCase(),
                }
            }
        },

        mounted() {
            this.places = places(Object.assign({container: this.$refs.input},
                this.defaultOptions, this.options
            ));

            // Set initial value.
            if (this.value) {
                this.places.setVal(this.value.name);
            }

            this.places.on('change', e => this.setValue(this.buildLocation(e.suggestion)));

            this.places.on('clear', () => this.setValue(null));
        }
    }
</script>
