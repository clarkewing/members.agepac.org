<template>
    <form class="mt-3" @submit.prevent="onSubmit">
        <div class="form-group" v-for="(option, index) in options" :key="index">
            <div v-if="max_votes == 1">
                <input
                    type="radio"
                    v-bind:id="option.id"
                    v-bind:value="option.id"
                    v-model="option_ids[0]"
                />
                <label v-bind:for="option.id">{{ option.label }}</label>
            </div>
            <div v-else>
                <input
                    type="checkbox"
                    v-bind:id="option.id"
                    v-bind:value="option.id"
                    v-model="option_ids"
                />
                <label v-bind:for="option.id">{{ option.label }}</label>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Voter</button>
    </form>
</template>

<script>
import LaravelInput from "./Inputs/LaravelInput";

export default {
    props: ["channelslug", "thread", "poll"],

    data() {
        var res = {
            ids: [],
            options: [],
            option_ids: [undefined],
            edition: false,
            max_votes: 1,
        };
        return res;
    },

    created() {
        let uri = "/poll/" + this.poll.id;
        axios
            .get(uri)
            .then((poll_data) => {
                this.max_votes = poll_data.max_votes;
            })
            .catch((error) => {
                flash(error.response.data, "danger");
            });

        uri =
            "/threads/" + this.channelslug + "/" + this.thread.slug + "/poll/options";
        axios
            .get(uri)
            .then((options_data) => {
                options_data.data.forEach((option) => {
                    this.options.push({
                        id: option.id,
                        label: option.label,
                        color: option.color,
                    });
                });
            })
            .catch((error) => {
                flash(error.response.data, "danger");
            });

        uri =
            "/threads/" + this.channelslug + "/" + this.thread.slug + "/poll/vote";
        axios
            .get(uri)
            .then((vote) => {
                if (vote.data.length > 0) {
                    this.edition = true;
                    this.option_ids = [];

                    vote.data.forEach((v) => {
                        this.ids.push(v.id);
                        this.option_ids.push(v.option_id);
                    });
                }
            })
            .catch((error) => {
                flash(error.response.data, "danger");
            });
    },

    methods: {
        onSubmit() {
            console.log(this.edition);
            if (this.edition) {
                let delRequests = this.ids.map((id) => {
                    let uri = "/poll-vote/" + id;
                    return axios.delete(uri);
                });
                Promise.all(delRequests)
                    .then(this.castVote)
                    .catch((error) => {
                        flash(error.response.data, "danger");
                    });
                this.ids = [];
            } else {
                this.castVote();
            }
        },
        castVote() {
            this.option_ids
                .filter((option_id) => option_id != undefined)
                .forEach((option_id) => {
                    let uri = "/poll/" + this.poll.id + "/vote/" + option_id;
                    axios
                        .post(uri)
                        .then((vote) => {
                            this.ids.push(vote.data.id);
                            flash("Ton vote a été pris en compte");
                        })
                        .catch((error) => {
                            flash(error.response.data, "danger");
                        });
                });
        },
    },
};
</script>
