<template>
    <input
        type="text"
        ref="input"
        class="form-control"
    >
</template>

<script>
import placekitAutocomplete from '@placekit/autocomplete-js'
import '@placekit/autocomplete-js/dist/placekit-autocomplete.css'

export default {
    props: {
        value: Object,
        options: {
            type: Object,
            default: function () {
                return {}
            }
        }
    },

    data() {
        return {
            pka: null,

            defaultOptions: {
                language: 'fr',
            }
        }
    },

    methods: {
        setValue(location) {
            this.$emit('input', location)
        },

        buildLocation(place) {
            return {
                type: place.type,
                name: place.name,
                street_line_1: place.type === 'street' ? place.name : null,
                street_line_2: null,
                municipality: place.city || place.name,
                administrative_area: place.administrative,
                sub_administrative_area: place.county || null,
                postal_code: place.zipcode[0] || null,
                country: place.country,
                country_code: place.countrycode.toUpperCase(),
            }
        }
    },

    mounted() {
        this.pka = placekitAutocomplete(
            App.config.placekit.key,
            Object.assign(
                {target: this.$refs.input},
                this.defaultOptions,
                this.options,
            )
        )

        // Set initial value.
        if (this.value) {
            this.pka.setValue(this.value.name)
        }

        this.pka.on('pick', (value, item) => this.setValue(this.buildLocation(item)))

        this.pka.on('empty', e => e && this.setValue(null))
    }
}
</script>
