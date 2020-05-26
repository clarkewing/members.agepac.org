<template>
    <div>
        <button type="button" :class="['btn', btnClass]" @click="openModal">
            <slot name="button">Add Payment Method</slot>
        </button>

        <div ref="modal" class="modal fade" tabindex="-1" role="dialog"
             data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un moyen de paiement</h5>
                    </div>

                    <div class="modal-body px-5 py-4">
                        <div class="alert alert-danger" role="alert" v-if="alert" v-text="alert"></div>

                        <div class="form-group">
                            <label for="name" class="small mb-1">Nom du titulaire de la carte</label>
                            <input type="text" :class="['form-control', nameError ? 'is-invalid' : '']" id="name"
                                   v-model="name" @focus="selectName">
                            <div class="invalid-feedback" v-text="nameError"></div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="cardElement" class="small mb-1">Carte</label>
                            <div id="cardElement" ref="cardElement" class="form-control"></div>
                            <div class="invalid-feedback" v-text="cardError"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary mr-auto"
                                :disabled="working" data-dismiss="modal">
                            Annuler
                        </button>

                        <button type="button" class="btn btn-success" :disabled="working" @click="addCard">
                            <span v-if="working">
                                <span class="spinner-border spinner-border-sm mr-1" role="status">
                                    <span class="sr-only">Loading...</span>
                                </span>
                                Bip-boop-bop
                            </span>

                            <span v-else>Ajouter</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            btnClass: String,

            styles: {
                type: Object,
                default: function () {
                    return {
                        base: {
                            iconColor: '#6c757d',
                            color: '#495057',
                            fontWeight: 400,
                            fontFamily: 'Raleway, sans-serif',
                            fontSize: '14.4px', // .9rem

                            '::placeholder': {
                                color: '#6c757d',
                            },
                        },
                        invalid: {
                            iconColor: '#e3342f',
                            color: '#e3342f',
                        }
                    };
                }
            }
        },

        data() {
            return {
                name: App.user.name,

                intent: null,
                stripe: null,
                elements: null,
                card: null,

                alert: '',
                cardError: '',
                nameError: '',
                working: false,
            }
        },

        methods: {
            addCard() {
                if (! this.validate()) return;

                this.working = true;

                this.stripe.confirmCardSetup(this.intent.client_secret, {
                        payment_method: {
                            card: this.card,
                            billing_details: {
                                name: this.name
                            }
                        }
                    })
                    .then(({setupIntent, error}) => {
                        if (error) {
                            this.alert = error.message;
                            this.working = false;

                            return;
                        }

                        // Send the token to server.
                        this.postCard(setupIntent);
                    });
            },

            fetchIntent() {
                if (this.intent) {
                    return;
                }

                axios.get('/account/subscription/payment-methods/create')
                    .then(({data}) => {
                        this.intent = data.intent;
                    });
            },

            initializeStripe() {
                if (this.stripe) {
                    return;
                }

                this.stripe = Stripe(App.config.cashier.key);

                this.elements = this.stripe.elements({
                    fonts: [{ cssSrc: 'https://fonts.googleapis.com/css?family=Raleway:400' }]
                });

                this.card = this.elements.create('card', {style: this.styles});
                this.card.mount('#cardElement');

                // Handle real-time validation errors from the card Element.
                this.card.on('change', (event) => {
                    if (event.error) {
                        this.cardError = event.error.message;
                    } else {
                        this.cardError = '';
                    }
                });
            },

            openModal() {
                $(this.$refs.modal).modal('show');

                this.initializeStripe();
                this.fetchIntent();
            },

            postCard(setupIntent) {
                axios.post('/account/subscription/payment-methods', {
                    payment_method: setupIntent.payment_method
                }, {
                    validateStatus: function (status) {
                        return status === 201; // Created
                    }
                })
                .then(() => location.reload());
            },

            selectName(e) {
                // Select full name on focus if prefilled.
                if (e.target.value === App.user.name) {
                    e.target.setSelectionRange(0, e.target.value.length);
                }
            },

            validate() {
                this.alert = '';
                this.nameError = '';

                if (! this.name.length) {
                    this.nameError = 'Le nom du titulaire est obligatoire.';

                    return false;
                }

                return true;
            }
        }
    }
</script>
