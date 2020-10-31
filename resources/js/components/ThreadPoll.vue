<template>
    <div>
        <div v-if="poll" class="card mb-4">
            <div class="card-body">
                <div class="d-flex">
                    <h5 class="card-title flex-grow-1" v-text="poll.title"></h5>

                    <div class="d-flex align-items-center flex-shrink-0 align-self-start">
                        <h6 class="text-muted mb-1">Sondage</h6>

                        <div v-if="canEdit || canDelete" class="dropdown ml-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown">
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear-wide-connected" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8.932.727c-.243-.97-1.62-.97-1.864 0l-.071.286a.96.96 0 0 1-1.622.434l-.205-.211c-.695-.719-1.888-.03-1.613.931l.08.284a.96.96 0 0 1-1.186 1.187l-.284-.081c-.96-.275-1.65.918-.931 1.613l.211.205a.96.96 0 0 1-.434 1.622l-.286.071c-.97.243-.97 1.62 0 1.864l.286.071a.96.96 0 0 1 .434 1.622l-.211.205c-.719.695-.03 1.888.931 1.613l.284-.08a.96.96 0 0 1 1.187 1.187l-.081.283c-.275.96.918 1.65 1.613.931l.205-.211a.96.96 0 0 1 1.622.434l.071.286c.243.97 1.62.97 1.864 0l.071-.286a.96.96 0 0 1 1.622-.434l.205.211c.695.719 1.888.03 1.613-.931l-.08-.284a.96.96 0 0 1 1.187-1.187l.283.081c.96.275 1.65-.918.931-1.613l-.211-.205a.96.96 0 0 1 .434-1.622l.286-.071c.97-.243.97-1.62 0-1.864l-.286-.071a.96.96 0 0 1-.434-1.622l.211-.205c.719-.695.03-1.888-.931-1.613l-.284.08a.96.96 0 0 1-1.187-1.186l.081-.284c.275-.96-.918-1.65-1.613-.931l-.205.211a.96.96 0 0 1-1.622-.434L8.932.727zM8 12.997a4.998 4.998 0 1 0 0-9.995 4.998 4.998 0 0 0 0 9.996z"/>
                                    <path fill-rule="evenodd" d="M7.375 8L4.602 4.302l.8-.6L8.25 7.5h4.748v1H8.25L5.4 12.298l-.8-.6L7.376 8z"/>
                                </svg>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right">
                                <button v-if="canEdit" type="button" @click="openForm" class="dropdown-item">
                                    Modifier le sondage
                                </button>
                                <button v-if="canDelete" type="button" @click="confirmDelete" class="dropdown-item text-danger">
                                    Supprimer le sondage
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <form v-if="panel === 'ballot'" @submit.prevent="castVote">
                    <div v-for="option of poll.options" class="form-check mb-1">
                        <input class="form-check-input"
                               type="checkbox"
                               v-model="vote"
                               name="poll_options"
                               :id="`poll_option_${option.id}`" :value="option.id"
                               :disabled="! canCastVote || (! canSelectMoreOptions && ! vote.includes(option.id))">
                        <label class="form-check-label" :for="`poll_option_${option.id}`" v-text="option.label"></label>
                    </div>

                    <div class="d-flex mt-3">
                        <button type="submit" class="btn btn-success" :disabled="! canCastVote">
                            <span v-if="canCastVote">Soumettre</span>
                            <span v-else>Tu ne peux pas modifier ton vote</span>
                        </button>

                        <button v-if="canViewResults" type="button" @click="showResults" class="btn btn-outline-secondary ml-2">
                            Voir les résultats
                        </button>
                    </div>
                </form>

                <div v-else-if="panel === 'results'">
                    <thread-poll-results ref="results"></thread-poll-results>

                    <div class="d-flex mt-3">
                        <button type="button" @click="showBallot" class="btn btn-outline-secondary">
                            Voir mon bulletin
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <thread-poll-form ref="form" @created="updateData" @updated="updateData" />
    </div>
</template>

<script>
import axios from "axios";
import ThreadPollForm from "./ThreadPollForm";
import ThreadPollResults from "./ThreadPollResults";

export default {
    components: { ThreadPollResults, ThreadPollForm },

    inject: ['thread'],
    props: ['initialPoll'],

    data() {
        return {
            poll: this.initialPoll,
            vote: this.initialPoll
                ? this.initialPoll.vote.map(o => o['option_id'])
                : [],
            hasVoted: this.initialPoll
                ? this.initialPoll.vote.length > 0
                : false,

            panel: 'ballot',
        }
    },

    computed: {
        canCastVote() {
            return ! this.isLocked && (! this.vote.length || this.poll.votes_editable);
        },

        canSelectMoreOptions() {
            return this.poll.max_votes === null || this.vote.length < this.poll.max_votes;
        },

        canViewResults() {
            return this.results_before_voting || this.hasVoted;
        },

        isLocked() {
            return this.poll.locked_at !== null && new Date(this.locked_at) < new Date();
        },

        canEdit() {
            return ! this.isLocked
                && ! this.poll.has_votes
                && this.canDelete;
        },

        canDelete() {
            return ! this.thread.locked
                && (
                    this.authorize('owns', this.thread)
                    || this.authorize('hasPermission', 'threads.edit')
                );
        },
    },

    methods: {
        openForm() {
            this.$refs.form.show();
        },

        confirmDelete() {
            if (window.confirm('Es-tu sûr de vouloir supprimer ce sondage ? Cette action est irréversible.')) {
                axios.delete(`/threads/${this.thread.channel.slug}/${this.thread.slug}/poll`)
                    .then(() => {
                        this.poll = null;

                        flash('Le sondage a été supprimé.');
                    })
                    .catch(() => {
                        flash('Une erreur s\'est produite.', 'danger');
                    });
            }
        },

        castVote() {
            let uri = `/threads/${this.thread.channel.slug}/${this.thread.slug}/poll/vote`;

            axios.put(uri, {
                vote: this.vote,
            })
                .then(() => {
                    flash('Merci d\'avoir participé au sondage !');

                    this.hasVoted = true;
                });
        },

        updateData(data) {
            this.poll = data;
        },

        showResults() {
            this.panel = 'results';

            this.$nextTick(() => this.$refs.results.loadResults());
        },

        showBallot() {
            this.panel = 'ballot';
        },
    },

    created() {
        if (this.poll && this.poll.vote.length) {
            this.showResults();
        }
    },

    provide() {
        return {
            poll: this.poll,
        };
    },
}
</script>
