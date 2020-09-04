<template>
  <form class="mt-3" @submit.prevent="onSubmit">
    <div class="form-group" v-for="(option, index) in options" :key="index">
      <input type="radio" v-bind:id="option.id" v-bind:value="option.id" v-model="option_id" />
      <label v-bind:for="option.id">{{ option.label }}</label>
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
      id: undefined,
      options: [],
      option_id: undefined,
      edition: false,
    };
    return res;
  },

  created() {
    let uri =
      "/threads/" +
      this.channelslug +
      "/" +
      this.thread.slug +
      "/poll/" +
      this.poll.id +
      "/options";
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
      "/threads/" +
      this.channelslug +
      "/" +
      this.thread.slug +
      "/poll/" +
      this.poll.id +
      "/vote";
    axios
      .get(uri)
      .then((vote) => {
        if (vote.data.length > 0) {
          this.id = vote.data[0].id;
          this.option_id = vote.data[0].option_id;
          this.edition = true;
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
        let uri = "/poll-vote/" + this.id;
        axios
          .delete(uri)
          .then(() => {
            this.castVote();
          })
          .catch((error) => {
            flash(error.response.data, "danger");
          });
      } else {
        this.castVote();
      }
    },
    castVote() {
      let uri = "/poll/" + this.poll.id + "/vote/" + this.option_id;
      axios
        .post(uri)
        .then((vote) => {
          this.id = vote.data.id;
          flash("Ton vote a été pris en compte");
        })
        .catch((error) => {
          flash(error.response.data, "danger");
        });
    },
  },
};
</script>