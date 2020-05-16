<template>
    <pagination :data="data"
                :limit="limit"
                :show-disabled="showDisabled"
                :size="size"
                :align="align"
                @pagination-change-page="onPaginationChangePage">
        <template #prev-nav>
            <slot name="prev-nav">‹</slot>
        </template>
        <template #next-nav>
            <slot name="next-nav">›</slot>
        </template>
    </pagination>
</template>

<script>
    import Pagination from 'laravel-vue-pagination';

    export default {
        components: {Pagination},

        props: {
            data: {
                type: Object,
                default: () => {}
            },
            limit: {
                type: Number,
                default: 2
            },
            showDisabled: {
                type: Boolean,
                default: true
            },
            size: {
                type: String,
                default: 'default',
                validator: value => {
                    return ['small', 'default', 'large'].indexOf(value) !== -1;
                }
            },
            align: {
                type: String,
                default: 'center',
                validator: value => {
                    return ['left', 'center', 'right'].indexOf(value) !== -1;
                }
            }
        },

        methods: {
            onPaginationChangePage (page) {
                this.$emit('pagination-change-page', page);
            }
        }
    }
</script>
