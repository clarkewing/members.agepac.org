<script>
    import algoliasearch from 'algoliasearch/lite';
    import { history } from 'instantsearch.js/es/lib/routers';
    import { simple } from 'instantsearch.js/es/lib/stateMappings';
    import ThreadResult from '../components/ThreadResult';

    export default {
        components: {ThreadResult},

        data() {
            return {
                searchClient: algoliasearch(
                    App.config.scout.algolia.id,
                    App.config.scout.algolia.key
                ),
                routing: {
                    router: history(),
                    stateMapping: simple(),
                }
            };
        },

        methods: {
            onPageChange() {
                window.scrollTo(0,0);
            }
        }
    };
</script>
