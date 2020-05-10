<form ref="formCredentials"
      role="tabpanel"
      class="tab-pane fade"
      @submit.prevent="postFormCredentials">

    <h5 class="text-center mb-3">Identifiants</h5>

    <laravel-input label="Adresse email"
                   name="email"
                   v-model="email"
                   type="email"
                   autocomplete="email"
                   help-text="Utilise ton adresse personnelle."
                   placeholder="marc.houalla@enac.fr"
                   required></laravel-input>

    <laravel-input label="Mot de passe"
                   name="password"
                   v-model="password"
                   type="password"
                   autocomplete="new-password"
                   help-text="Choisis quelque chose de sûr et d'au moins 8 caractères."
                   placeholder="SayHelloToMyLittleFriend"
                   required></laravel-input>

    <laravel-input label="Confirmation"
                   class="mb-4"
                   name="password_confirmation"
                   v-model="password_confirmation"
                   type="password"
                   autocomplete="new-password"
                   required></laravel-input>

    <button type="submit"
            class="btn btn-primary rounded-pill d-flex align-items-center mx-auto">
        <span>Continuer</span>

        <svg class="bi bi-arrow-right-short ml-2" width="1em" height="1em" viewBox="0 0 16 16"
             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M8.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L10.793 8 8.146 5.354a.5.5 0 010-.708z"
                  clip-rule="evenodd"/>
            <path fill-rule="evenodd" d="M4 8a.5.5 0 01.5-.5H11a.5.5 0 010 1H4.5A.5.5 0 014 8z"
                  clip-rule="evenodd"/>
        </svg>
    </button>
</form>
