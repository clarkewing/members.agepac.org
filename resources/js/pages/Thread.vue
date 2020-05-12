<script>
    import Posts from '../components/Posts.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['thread'],

        components: { Posts, SubscribeButton },

        data() {
            return {
                repliesCount: this.thread.replies_count,
                locked: this.thread.locked,
                pinned: this.thread.pinned,
                title: this.thread.title,
                body: this.thread.body,
                form: {},
                editing: false
            }
        },

        created () {
            this.resetForm();
        },

        methods: {
            toggleLock () {
                let uri = `/locked-threads/${this.thread.slug}`;

                axios[this.locked ? 'delete' : 'post'](uri)
                    .then(() => {
                        this.locked = ! this.locked;
                    });
            },


            togglePin () {
                let uri = `/pinned-threads/${this.thread.slug}`;

                axios[this.pinned ? 'delete' : 'post'](uri)
                    .then(() => {
                        this.pinned = ! this.pinned;
                    });
            },

            update () {
                let uri = `/threads/${this.thread.channel.slug}/${this.thread.slug}`;

                axios.patch(uri, this.form)
                    .then(() => {
                        this.title = this.form.title;
                        this.body = this.form.body;

                        this.editing = false;

                        flash('La discussion a été modifiée.')
                    });
            },

            resetForm () {
                this.form = {
                    title: this.thread.title,
                    body: this.thread.body
                };

                this.editing = false;
            },

            classes(target) {
                return [
                    this[target] ? 'text-primary' : 'text-muted'
                ];
            }
        }
    }
</script>
