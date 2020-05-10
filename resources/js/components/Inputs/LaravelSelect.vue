<template>
    <div class="form-group">
        <label :for="id" v-text="label" v-if="label"></label>

        <select :class="['form-control', (errors[name] || invalid) ? 'is-invalid' : '']"
                :id="id"
                :name="name"
                v-on:input="$emit('input', $event.target.value)"
                v-bind="$attrs">

            <option v-text="placeholder"
                    value=""
                    :selected="! value"
                    disabled></option>

            <option v-for="(optionLabel, optionValue) of options"
                    :value="optionsIsArray ? optionLabel : optionValue"
                    :selected="value == (optionsIsArray ? optionLabel : optionValue)"
                    v-text="optionLabel"></option>

        </select>

        <small class="form-text text-muted"
               :id="helpId"
               v-if="helpText"
               v-text="helpText"></small>

        <div class="invalid-feedback"
             v-if="errors[name]"
             v-text="errors[name][0]"></div>
    </div>
</template>

<script>
    import laravelInput from '../../mixins/laravel-input';

    export default {
        mixins: [laravelInput],

        props: {
            options: {
                type: [Object, Array],
                required: true
            },

            placeholder: {
                type: String
            }
        },

        computed: {
            optionsIsArray: function () {
                return _.isArray(this.options);
            }
        }
    }
</script>
