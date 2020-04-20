<script>
    import algoliasearch from 'algoliasearch/lite';

    export default {
        data() {
            return {
                searchClient: algoliasearch(
                    App.config.scout.algolia.id,
                    App.config.scout.algolia.key
                )
            };
        },
    };
</script>
