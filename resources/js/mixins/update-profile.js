import crudResouce from './crud-resource';

export default {
    mixins: [crudResouce],

    data() {
        return {
            username: window.location.pathname.split('/')[2],
        };
    },

    computed: {
        canUpdate() {
            return this.authorize(user => user.username === this.username);
        }
    }
}
