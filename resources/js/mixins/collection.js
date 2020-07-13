export default {
    data() {
        return {
            items: []
        };
    },

    methods: {
        add(item) {
            this.items.push(item);

            this.$emit('added');
        },

        remove(index) {
            this.items.splice(index, 1);

            this.$emit('removed');
        },

        removeId(id) {
            this.items = _.filter(this.items, (item) => (item.id !== id));

            this.$emit('removed');
        },
    }
}
