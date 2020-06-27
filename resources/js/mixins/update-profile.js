import {Form} from 'vform';

export default {
    props: ['data'],

    data() {
        return {
            endpoint: '',

            form: new Form(this.data),

            fields: this.data
        };
    },

    methods: {
        update() {
            this.beforeUpdate();

            this.form.patch(this.endpoint)
                .then(({data}) => {
                    this.form.fill(data);
                    this.fillFields(data);

                    this.success();
                });
        },

        beforeUpdate() {},

        success() {},

        fillFields(data) {
            Object.keys(this.fields).forEach(key => {
                this.fields[key] = data[key];
            });
        }
    }
}
