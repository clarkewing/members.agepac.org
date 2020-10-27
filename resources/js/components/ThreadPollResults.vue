<template>
    <div>
        <div v-if="working" class="text-center py-5">
            <span class="spinner-border spinner-border-sm mr-1" role="status">
                <span class="sr-only">Chargement...</span>
            </span>
            Bip-boop-bop
        </div>

        <div v-else v-for="(option, index) of results" class="row align-items-center">
            <div class="col-md-3">
                <button v-if="option.voters"
                        type="button"
                        class="btn btn-link text-left p-0"
                        @click="showVoters(index)"
                        v-text="option.label"></button>

                <span v-else v-text="option.label"></span>
            </div>

            <div class="col-md-9">
                <div class="progress">
                    <div class="progress-bar"
                         :style="{width: `${option.votes_percent}%`, background: option.color}"
                    >
                        {{ option.votes_count }} ({{ option.votes_percent }}%)
                    </div>
                </div>
            </div>
        </div>

        <div ref="voterListModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-text="selectedOption ? selectedOption.label : ''"></h5>

                        <button type="button"
                                data-dismiss="modal"
                                class="close"
                        >
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="modal-body">
                        <ul v-if="selectedOption && selectedOption.voters && selectedOption.voters.length">
                            <li v-for="voter of selectedOption.voters">
                                <a :href="`/profiles/${voter.username}`">{{ voter.first_name }} {{ voter.last_name }}</a>
                            </li>
                        </ul>

                        <div v-else class="text-center">
                            Aucun vote pour cette option.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    inject: ['thread'],

    data() {
        return {
            results: [],
            selectedOption: null,

            working: true,
        }
    },

    methods: {
        loadResults() {
            this.working = true;

            axios.get(`/threads/${this.thread.channel.slug}/${this.thread.slug}/poll/results`)
                .then(({data}) => {
                    this.results = data;

                    this.working = false;
                });
        },

        showVoters(index) {
            this.selectedOption = this.results[index];

            $(this.$refs.voterListModal).modal('show');
        },
    }
}
</script>
