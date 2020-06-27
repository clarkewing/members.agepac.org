import {Form} from 'vform';

export default {
    props: ['data'],

    data() {
        return {
            endpoint: '',
            resourceId: null,

            form: new Form(this.data),

            fields: this.data
        };
    },

    methods: {
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
