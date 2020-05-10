<script>
    import LaravelInput from "../components/Inputs/LaravelInput";
    import LaravelSelect from "../components/Inputs/LaravelSelect";
    import moment from 'moment';

    export default {
        components: {
            LaravelInput,
            LaravelSelect
        },

        props: {
            config: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                name: null,

                first_name: null,
                last_name: null,
                class_course: null,
                class_year: (new Date()).getFullYear(),

                email: null,
                password: null,
                password_confirmation: null,

                birthdate_day: null,
                birthdate_month: null,
                birthdate_year: null,
                gender: null,
                phone: null,

                invitationFound: false,
                errors: {},
                complete: false,

                listOfDays: this.range(1, 31),
                listOfMonths: {
                    1: 'Janvier',
                    2: 'Février',
                    3: 'Mars',
                    4: 'Avril',
                    5: 'Mai',
                    6: 'Juin',
                    7: 'Juillet',
                    8: 'Août',
                    9: 'Septembre',
                    10: 'Octobre',
                    11: 'Novembre',
                    12: 'Décembre',
                },
                listOfYears: this.range((new Date()).getFullYear() - 13, (new Date()).getFullYear() - 100),
            }
        },

        computed: {
            class_full: function () {
                return this.class_course + ' ' + this.class_year;
            },

            birthdate: function () {
                let dateObj = moment([
                    this.birthdate_year,
                    parseInt(this.birthdate_month) - 1,
                    this.birthdate_day,
                ]);

                if (dateObj.isValid()) {
                    return dateObj;
                } else {
                    return null;
                }
            }
        },

        mounted() {
            this.showTab('formName');
        },

        methods: {
            postFormName: function () {
                this.findInvitation({
                    name: this.name
                })
                .then(() => {
                    if (!this.invitationFound) this.setFirstAndLastFromName(this.name);

                    this.showTab('formIdentity')
                });
            },

            postFormIdentity: function () {
                this.findInvitation({
                    first_name: this.first_name,
                    last_name: this.last_name,
                    class_course: this.class_course,
                    class_year: this.class_year
                })
                .then(() => {
                    if (this.invitationFound) {
                        this.showTab('formCredentials');
                    } else {
                        this.showTab('formNoInvitation');
                    }
                });
            },

            postFormCredentials: function () {
                this.errors = {};

                if (this.password.length < 8) {
                    this.errors = {'password': ['Le mot de passe doit contenir au moins 8 caractères.']};

                } else if (this.password !== this.password_confirmation) {
                    this.errors = {'password_confirmation': ['Le champ de confirmation mot de passe ne correspond pas.']};

                } else {
                    this.showTab('formDetails');
                }
            },

            postFormDetails: function () {
                this.errors = {};

                if (! this.birthdate) {
                    this.errors = {'birthdate': ['La date de naissance saisie n\'est pas valide.']};

                } else if (! Object.keys(this.config.genders).includes(this.gender)) {
                    this.errors = {'gender': ['Le champ genre est invalide.']};

                } else {
                    this.showTab('formSummary');
                }
            },

            postFormSummary: function () {
                axios.post('/register', {
                    first_name: this.first_name,
                    last_name: this.last_name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    class_course: this.class_course,
                    class_year: this.class_year,
                    gender: this.gender,
                    birthdate: this.birthdate.format('YYYY-MM-DD'),
                    phone: this.phone,
                })
                .catch(this.handleErrors())
                .catch((error) => {
                    if (! _.isEmpty(this.errors)) {
                        if (this.errors.email || this.errors.password || this.errors.password_confirmation) {
                            this.showTab('formCredentials');
                        } else if (this.errors.birthdate || this.errors.gender || this.errors.phone) {
                            this.showTab('formDetails');
                        } else {
                            console.error('Unhandled error: ' + JSON.stringify(this.errors));
                        }

                        return Promise.reject('The given data was invalid.');
                    }

                    return Promise.reject(JSON.stringify(error));
                })
                .then(({data}) => {
                    this.complete = true;

                    this.showTab('formSuccess');
                    setTimeout(() => this.$refs.hello.classList.add('play'), 250);
                });
            },


            showTab: function (tab) {
                $('.tab-pane.show').removeClass(['show', 'active']);

                $(this.$refs[tab]).tab('show');
            },

            findInvitation: function (params) {
                return axios.get('/api/user-invitations', {params: params})
                    .catch(this.handleErrors())
                    .then(({data}) => {
                        this.errors = {};

                        if (_.isEmpty(data)) {
                            this.invitationFound = false;
                        } else {
                            this.first_name = data.first_name;
                            this.last_name = data.last_name;
                            this.name = data.first_name + ' ' + data.last_name;
                            this.class_course = data.class_course;
                            this.class_year = data.class_year;

                            this.invitationFound = true;
                        }
                    });
            },

            setFirstAndLastFromName: function (name) {
                let [first_name, ...last_name] = name.split(' ');
                last_name = last_name.join(' ');

                this.first_name = _.startCase(_.toLower(first_name));
                this.last_name = _.startCase(_.toLower(last_name));
            },

            range: function (first, last) {
                if (first <= last) {
                    last++;
                } else {
                    last--;
                };

                return _.range(first, last);
            },

            handleErrors: function () {
                return ({response}) => {
                    this.errors = {};

                    if (response.status === 422) {
                        this.errors = response.data.errors;
                    } else {
                        this.showTab('formServerError');
                    }

                    return Promise.reject(response.data.message);
                };
            }
        }
    }
</script>
