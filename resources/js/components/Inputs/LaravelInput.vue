<template>
    <div class="form-group">
        <label :for="id" v-text="label" v-if="label"></label>

        <input :type="type"
               :class="['form-control', (errors[name] || invalid) ? 'is-invalid' : '']"
               :id="id"
               :name="name"
               :value="value"
               v-on:input="$emit('input', $event.target.value)"
               v-bind="$attrs">

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
            type: {
                type: String,
                default: 'text'
            }
        }
    }
</script>
