<script>
    export default {
        data() {
            return {
                desiredPlan: 'agepac',

                plans: {
                    'agepac': {
                        name: 'AGEPAC',
                        price: 3000 // in cents
                    },
                    'agepac+alumni': {
                        name: 'AGEPAC + ENAC Alumni',
                        price: 6000 // in cents
                    }
                },

                acceptedTerms1: false,
                acceptedTerms2: false,
                acceptedTerms3: false
            }
        },

        methods: {
            acceptTerms(section) {
                this['acceptedTerms' + section] = true;

                $('#subscriptionTerms' + (section + 1).toString()).collapse('toggle');
            },

            startSubscription(plan) {
                this.desiredPlan = plan;

                $(this.$refs.createSubModal).modal('show');
            },

            toEuros(amount) {
                return (amount / 100).toLocaleString("fr-FR", {style:'currency', currency:'EUR'});
            }
        }
    }
</script>
