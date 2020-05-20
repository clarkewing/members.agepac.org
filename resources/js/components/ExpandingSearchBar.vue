<template>
    <div ref="container" :class="['input-group-expanding', expanded ? 'open' : '', classes]" :id="id">
        <input ref="input"
               class="form-control"
               v-bind="$attrs"
               :name="name"
               :value="value"
               v-on:input="$emit('input', $event)">
        <button ref="btn" class="btn btn-secondary d-flex justify-content-center" :type="btnType" @click="handleBtn">
            <slot name="btn">Go</slot>
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            classes: String,
            name: String,
            value: {
                type: String,
                default: ''
            },
            id: String,
            btnType: {
                type: String,
                default: 'button'
            }
        },

        data() {
            return {
                expanded: !! this.value.length
            }
        },

        methods: {
            handleBtn(event) {
                if (! this.expanded) {
                    this.expandInput();

                    event.preventDefault();
                }
            },

            expandInput() {
                if (! this.expanded) {
                    this.$refs.btn.blur();

                    this.expanded = true;

                    setTimeout(() => this.$refs.input.focus(), 250);
                }
            },

            contractInput() {
                if (this.expanded) this.expanded = false;
            }
        }
    }
</script>
