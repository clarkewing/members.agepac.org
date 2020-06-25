import {Form} from 'vform';

export default {
    props: ['data'],

    data() {
        return {
            form: new Form(this.data),

            fields: this.data
        };
    },

    methods: {
        update() {
            this.beforeUpdate();

            this.form.patch('')
                .then(({data}) => {
                    this.form.fill(data);
                    this.fillFields(data);

                    this.success();
                });
        },

        beforeUpdate() {},

        fillFields(data) {
            Object.keys(this.fields).forEach(key => {
                this.fields[key] = data[key];
            });
        }
    }
}
