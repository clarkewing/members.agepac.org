import {Form} from 'vform';

export default {
    props: ['data'],

    data() {
        return {
            username: window.location.pathname.split('/')[2],

            endpoint: '',
            resourceId: null,

            form: new Form(this.data),

            fields: this.data
        };
    },

    computed: {
        canUpdate() {
            return this.authorize(user => user.username === this.username);
        }
    },

    methods: {
        create() {
            this.form.post(this.endpoint, {
                validateStatus: function (status) {
                    return status === 201; // HTTP_CREATED
                }
            }).then(({data}) => {
                this.form.reset();

                this.created(data);
            });
        },

        update() {
            this.form.patch(this.resourceEndpoint())
                .then(({data}) => {
                    this.form.fill(data);
                    this.fillFields(data);

                    this.updated();
                });
        },

        destroy() {
            axios.delete(this.resourceEndpoint(), {
                validateStatus: function (status) {
                    return status === 204; // HTTP_NO_CONTENT
                }
            }).then(() => {
                this.deleted();
            });
        },

        updated() {},
        deleted() {},

        fillFields(data) {
            Object.keys(this.fields).forEach(key => {
                this.fields[key] = data[key];
            });
        },

        resourceEndpoint() {
            if (this.resourceId === null) {
                return this.endpoint;
            }

            return this.endpoint + '/' + this.resourceId;
        }
    }
}
