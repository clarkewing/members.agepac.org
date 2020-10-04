<template>
  <div>
    <p>Résultats du sondage</p>
    <div class="form-group" v-for="(option, index) in options" :key="index">
      <div class="row">
        <div class="col">{{ option.label }}</div>
        <div class="col">{{ option.count }} ({{total_votes === 0 ? 0 : (100*option.count/total_votes).toFixed(1)}} %)</div>
      </div>
    </div>
  </div>
</template>

<script>
import LaravelInput from "./Inputs/LaravelInput";

export default {
  props: ["channelslug", "thread", "poll"],

  data() {
    var res = {
      results: [],
      options: [],
      total_votes: 0,
    };
    return res;
  },

  created() {
    let uri =
      "/threads/" +
      this.channelslug +
      "/" +
      this.thread.slug +
      "/poll/options";
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
        uri =
          "/threads/" +
          this.channelslug +
          "/" +
          this.thread.slug +
          "/poll/votes";
        axios
          .get(uri)
          .then((results_data) => {
            results_data.data.forEach((opt_count) => {
              this.results.push(opt_count);
            });
            this.total_votes = 0;
            this.options = this.options.map((option) => {
              let count =
                this.results.find(
                  (opt_count) => opt_count.option_id === option.id
                )?.votes_number ?? 0;
              this.total_votes += count;
              return { ...option, count: count };
            });
            console.log(this.options);
          })
          .catch((error) => {
            flash(error.response.data, "danger");
          });
      })
      .catch((error) => {
        flash(error.response.data, "danger");
      });
  },

  methods: {},
};
</script>