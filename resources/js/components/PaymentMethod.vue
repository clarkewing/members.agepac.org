<template>
    <div class="card">
        <div class="card-body row">
            <div class="col-md d-flex align-items-center mb-3 mb-md-0">
                <img :src="logoSrc" alt="Card" class="mr-3" style="height: 1.75em">

                <span class="mr-auto mr-md-3" v-if="paymentMethod.card.brand === 'amex'">
                        ●●●● ●●●●●● ●{{ paymentMethod.card.last4 }}
                </span>
                <span class="mr-auto mr-md-3" v-else>
                        ●●●● ●●●● ●●●● {{ paymentMethod.card.last4 }}
                </span>

                <span>
                    Exp. {{ paymentMethod.card.exp_month }}/{{ paymentMethod.card.exp_year }}
                </span>
            </div>

            <div class="col-md-auto d-flex align-items-center">
                <button type="button" :disabled="isDefault" @click="setDefault"
                        :class="['btn btn-sm mr-auto mr-sm-2', isDefault ? 'btn-secondary' : 'btn-outline-secondary']">
                    <span v-if="isDefault">
                        <svg class="bi bi-check mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                        </svg>
                        Par défaut
                    </span>

                    <span v-else>Utiliser par défaut</span>
                </button>

                <span ref="deleteWrapper">
                    <button type="button" class="btn btn-sm btn-outline-danger" @click="remove"
                            :disabled="isDefault" :style="isDefault ? 'pointer-events: none;' : ''">
                    <svg class="bi bi-trash" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd"
                              d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                    <span class="sr-only">Supprimer</span>
                </button>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            paymentMethod: {
                type: Object,
                required: true
            }
        },

        computed: {
            logoSrc() {
                return '/images/card-icons/' + this.paymentMethod.card.brand + '.svg';
            }
        },

        data() {
            return {
                isDefault: this.paymentMethod.id === App.user.defaultPaymentMethod
            };
        },

        methods: {
            remove() {
                axios.delete('/account/billing/payment-methods/' + this.paymentMethod.id)
                    .then(() => {
                        flash('Le moyen de paiement a été supprimé.');

                        this.$destroy();
                        this.$el.parentNode.removeChild(this.$el);
                    });
            },

            setDefault() {
                axios.put('/account/billing/payment-methods/' + this.paymentMethod.id, {
                    default: true
                }).then(() => {
                    flash('Ton moyen de paiement par défaut a été modifié.');

                    window.events.$emit('default-payment-method-set', this.paymentMethod.id);
                });
            },

            setupDeleteTooltip() {
                if (this.isDefault) {
                    $(this.$refs.deleteWrapper).tooltip({
                        title: 'Tu ne peux pas supprimer ton moyen de paiement par défaut',
                        placement: 'left'
                    });
                } else {
                    $(this.$refs.deleteWrapper).tooltip('dispose');
                }
            }
        },

        mounted() {
            window.events.$on('default-payment-method-set', (defaultPaymentMethod) => {
                this.isDefault = this.paymentMethod.id === defaultPaymentMethod;
            });

            this.setupDeleteTooltip();
        },

        watch: {
            isDefault() {
                this.setupDeleteTooltip();
            }
        }
    }
</script>
