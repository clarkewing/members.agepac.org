<form ref="formSummary"
      role="tabpanel"
      class="tab-pane fade"
      @submit.prevent="postFormSummary">

    <h4 class="text-center mb-4">Vérification</h4>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="text-uppercase text-muted font-weight-bold mb-0">
            Nom et promotion
        </h6>
        <button type="button"
                class="btn btn-link btn-sm p-0"
                @click="showTab('formIdentity')">
            Modifier
        </button>
    </div>

    <dl class="border-bottom pb-2 mb-3">
        <dt>Nom</dt>
        <dd v-text="name"></dd>

        <dt>Promotion</dt>
        <dd v-text="class_full"></dd>
    </dl>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="text-uppercase text-muted font-weight-bold mb-0">
            Identifiants
        </h6>
        <button type="button"
                class="btn btn-link btn-sm p-0"
                @click="showTab('formCredentials')">
            Modifier
        </button>
    </div>

    <dl class="border-bottom pb-2 mb-3">
        <dt>Adresse email</dt>
        <dd v-text="email"></dd>

        <dt>Mot de passe</dt>
        <dd>&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</dd>
    </dl>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="text-uppercase text-muted font-weight-bold mb-0">
            Détails supplémentaires
        </h6>
        <button type="button"
                class="btn btn-link btn-sm p-0"
                @click="showTab('formDetails')">
            Modifier
        </button>
    </div>

    <dl class="mb-4">
        <dt>Date de naissance</dt>
        <dd v-if="birthdate" v-text="birthdate.format('DD/MM/YYYY')"></dd>

        <dt>Genre</dt>
        <dd v-text="config.genders[gender]"></dd>

        <dt>Numéro de téléphone</dt>
        <dd v-text="phone"></dd>
    </dl>

    <button type="submit"
            class="btn btn-success rounded-pill d-flex align-items-center mx-auto">
        <span>Terminé !</span>

        <svg class="bi flaticon-takeoff ml-2" width="1em" height="1em" viewBox="0 0 12 12"
             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M1.2 8.2c-.1.1-.1.2-.1.3.1.1.2.2.4.2l4-1.3-.8 1.2c-.1.1 0 .2 0 .3.1.1.2.1.3 0l.8-.4h.1L8.2 6l2.9-1.5c1.1-.6 1-1 .9-1.2 0-.1-.2-.2-.4-.3-.3-.1-.6-.1-1 0h-.3c-.3 0-.5 0-1.6.5L3 6.6.8 5.5H.5l-.3.2c-.1 0-.2.1-.2.2s0 .1.1.2L1.6 8l-.4.2z"/>
        </svg>
    </button>
</form>
