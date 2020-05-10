export default {
    props: {
        name: {
            type: String,
            required: true
        },

        label: String,

        helpText: String,

        value: null, // No type validation

        invalid: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        id: function () {
            return 'input_' + this.name;
        },

        helpId: function () {
            return this.id + 'HelpBlock';
        },

        errors: function () {
            return this.$parent.errors;
        }
    }
}
