<template>
  <form class="mt-3" @submit.prevent="onSubmit">
    <p>Création d'un sondage</p>
    <div class="form-group">
      <label for="title">Titre</label>
      <input type="text" class="form-control" id="title" name="title" v-model="title" required />
    </div>
    <div class="form-group">
      <label for="votes_editable">Les utilisateurs pourront-ils modifier leur vote ?</label>
      <input type="checkbox" v-model="votes_editable" />
    </div>
    <div class="form-group">
      <label for="results_before_voting">Résultats avant la clôture ?</label>
      <input type="checkbox" v-model="results_before_voting" />
    </div>
    <div class="form-group">
      <label for="votes_privacy">Visibilité des votes</label>
      <select v-model="votes_privacy">
        <option
          v-for="option in votes_privacy_options"
          v-bind:value="option.value"
          :key="option.value"
        >{{ option.text }}</option>
      </select>
    </div>
    <div class="form-group">
      <label for="max_votes">Nombre maximal de votes</label>
      <input
        type="text"
        class="form-control"
        id="max_votes"
        name="max_votes"
        v-model="max_votes"
        required
      />
    </div>
    <div
      class="form-group"
      v-for="(option, index) in options.filter(option => option.status != 'deleted')"
      :key="index"
    >
      <label for="option.label">Choix {{index + 1}}</label>
      <div v-if="index <= 1">
        <input
          type="text"
          class="form-control"
          id="index"
          name="option.label"
          v-model="option.label"
          required
        />
      </div>
      <div v-else>
        <input
          type="text"
          class="form-control"
          id="index"
          name="option.label"
          v-model="option.label"
        />
        <button class="btn btn-link" @click="removeOption(index, $event)">Retirer</button>
      </div>
    </div>
    <div class="form-group">
      <button class="btn btn-success" @click="addOption">Ajouter un choix</button>
    </div>
    <div class="form-group">
      <datetime format="DD/MM/YYYY H:i:s" width="300px" v-model="locked_at"></datetime>
    </div>
    <button type="submit" class="btn btn-success">Publier</button>
  </form>
</template>

<script>
import LaravelInput from "./Inputs/LaravelInput";
import datetime from "vuejs-datetimepicker";
import moment from "moment";

export default {
  data() {
    var res = {
      id: undefined,
      title: "",
      votes_editable: true,
      max_votes: 1000000,
      results_before_voting: true,
      votes_privacy: 0,
      votes_privacy_options: [
        { text: "Personne", value: 0 },
        { text: "Auteur", value: 1 },
        { text: "Tous", value: 2 },
      ],
      locked_at: null,
      options: [
        { id: undefined, status: "created", label: "", color: "#ffffff" },
        { id: undefined, status: "created", label: "", color: "#ffffff" },
      ],
      edition: false,
    };
    return res;
  },

  created() {
    axios
      .get(location.pathname.split("/create")[0])
      .then(({ data }) => {
        if (data.length > 0) {
          this.id = data[0].id;
          this.title = data[0].title;
          this.votes_editable = data[0].votes_editable;
          this.max_votes = data[0].max_votes;
          this.results_before_voting = data[0].results_before_voting;
          this.votes_privacy = data[0].votes_privacy;
          if (data[0].locked_at) {
            this.locked_at = moment(data[0].locked_at).format(
              "DD/MM/YYYY hh:mm:ss"
            );
          }
          this.edition = true;
          this.options = [];

          axios
            .get(
              location.pathname.split("/create")[0] + "/" + this.id + "/options"
            )
            .then((options_data) => {
              options_data.data.forEach((option) => {
                console.log(option);
                this.options.push({
                  id: option.id,
                  status: "updated",
                  label: option.label,
                  color: option.color,
                });
              });
            })
            .catch((error) => {
              flash(error.response.data, "danger");
            });
        }
      })
      .catch((error) => {
        flash(error.response.data, "danger");
      });
  },

  components: { datetime },

  methods: {
    addOption(event) {
      event.preventDefault();
      this.options.push({
        id: undefined,
        status: "created",
        label: "",
        color: "#ffffff",
      });
    },
    removeOption(index, event) {
      event.preventDefault();
      this.options = this.options.filter(option => option.status !== "deleted")
        .filter((item, ind) => ind !== index || ind !== undefined)
        .map((item, ind) =>
          ind === index ? { ...item, status: "deleted" } : item
        );
    },
    onSubmit() {
      if (this.edition) {
        this.updatePoll();
      } else {
        this.addPoll();
      }
    },
    updatePoll() {
      moment.locale("fr");
      axios
        .put(
          location.origin + "/poll/" + this.id + "/update",
          {
            title: this.title,
            votes_editable: this.votes_editable,
            max_votes: this.max_votes,
            results_before_voting: this.results_before_voting,
            votes_privacy: this.votes_privacy,
            locked_at: moment(this.locked_at, "DD/MM/YYYY hh:mm:ss").format(),
          }
        )
        .then(({ data }) => {
          flash("Ton sondage a été modifié !");

          this.$emit("updated", data);
        })
        .catch((error) => {
          flash(error.response.data, "danger");
        });

      this.options
        .filter((option) => option.status == "updated")
        .forEach((option) => {
          axios
            .put(
              location.origin + 
                "/poll-option/" +
                option.id +
                "/update",
              {
                label: option.label,
                color: option.color,
              }
            )
            .catch((error) => {
              flash(error.response.data, "danger");
            });
        });
        this.options
        .filter((option) => option.status == "created")
        .forEach((option) => {
          axios
            .post(
              location.pathname.split("/create")[0] + 
                "/" + this.id +
                "/poll-option",
              {
                label: option.label,
                color: option.color,
              }
            )
            .catch((error) => {
              flash(error.response.data, "danger");
            });
        });
        this.options
        .filter((option) => option.status == "deleted" && option.id !== undefined)
        .forEach((option) => {
          axios
            .delete(
              location.origin + 
                "/poll-option/" +
                option.id +
                "/delete"
            )
            .catch((error) => {
              flash(error.response.data, "danger");
            });
        });
    },
    addPoll() {
      moment.locale("fr");
      axios
        .post(location.pathname.split("/create")[0], {
          title: this.title,
          votes_editable: this.votes_editable,
          max_votes: this.max_votes,
          results_before_voting: this.results_before_voting,
          votes_privacy: this.votes_privacy,
          locked_at: moment(this.locked_at, "DD/MM/YYYY hh:mm:ss").format(),
          option_labels: this.options
            .filter((option) => option.status === "created")
            .map((option) => option.label),
          option_colors: this.options
            .filter((option) => option.status === "created")
            .map((option) => option.color),
        })
        .then(({ data }) => {
          flash("Ton sondage a été publié !");

          this.$emit("created", data);
        })
        .catch((error) => {
          flash(error.response.data, "danger");
        });
    },
  },
};
</script>
