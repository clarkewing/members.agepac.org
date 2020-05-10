<div ref="formIdentity"
     role="tabpanel"
     class="tab-pane fade">

    <h5 class="text-center mb-3">
        <span v-if="invitationFound">Trouvé !</span>
        <span v-else>Dis-nous en plus...</span>
    </h5>

    <div v-if="invitationFound">
        <dl class="mb-4">
            <dt>Prénom</dt>
            <dd v-text="first_name"></dd>

            <dt>Nom</dt>
            <dd v-text="last_name"></dd>

            <dt>Promotion</dt>
            <dd v-text="class_full"></dd>
        </dl>

        <button type="button"
                class="btn btn-primary rounded-pill d-flex align-items-center mx-auto mb-2"
                @click="showTab('formCredentials')">
            <span>C'est bien moi</span>

            <svg class="bi bi-arrow-right-short ml-2" width="1em" height="1em" viewBox="0 0 16 16"
                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M8.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L10.793 8 8.146 5.354a.5.5 0 010-.708z"
                      clip-rule="evenodd"/>
                <path fill-rule="evenodd" d="M4 8a.5.5 0 01.5-.5H11a.5.5 0 010 1H4.5A.5.5 0 014 8z"
                      clip-rule="evenodd"/>
            </svg>
        </button>

        <button type="button"
                class="btn btn-link d-flex align-items-center mx-auto"
                @click="invitationFound = false">
            <span>Ce n'est pas moi</span>
        </button>
    </div>


    <form v-else @submit.prevent="postFormIdentity">
        <laravel-input label="Prénom"
                       name="first_name"
                       v-model="first_name"
                       autocomplete="given-name"
                       placeholder="Marc"
                       required></laravel-input>

        <laravel-input label="Nom"
                       name="last_name"
                       v-model="last_name"
                       autocomplete="family-name"
                       placeholder="Houalla"
                       required></laravel-input>

        <laravel-select label="Cursus"
                        name="class_course"
                        v-model="class_course"
                        :options="config.courses"
                        required></laravel-select>

        <laravel-input label="Promotion"
                       class="mb-4"
                       name="class_year"
                       v-model="class_year"
                       type="number"
                       min="1900" max="2199" step="1"
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
</div>
