<form ref="formDetails"
      role="tabpanel"
      class="tab-pane fade"
      @submit.prevent="postFormDetails">

    <h5 class="text-center mb-3">Quelques derniers détails...</h5>

    <div class="form-row mb-3">
        <legend class="col-form-label">Date de naissance</legend>

        <laravel-select class="col-3 mb-0"
                        name="birthdate_day"
                        v-model="birthdate_day"
                        :options="listOfDays"
                        autocomplete="bday-day"
                        placeholder="Jour"
                        :invalid="!! errors.birthdate"
                        required></laravel-select>

        <laravel-select class="col-5 mb-0"
                        name="birthdate_month"
                        v-model="birthdate_month"
                        :options="listOfMonths"
                        autocomplete="bday-month"
                        placeholder="Mois"
                        :invalid="!! errors.birthdate"
                        required></laravel-select>

        <laravel-select class="col-4 mb-0"
                        name="birthdate_year"
                        v-model="birthdate_year"
                        :options="listOfYears"
                        autocomplete="bday-year"
                        placeholder="Année"
                        :invalid="!! errors.birthdate"
                        required></laravel-select>
    </div>
    <div class="invalid-feedback d-block mt-n2 mb-3"
         v-if="errors.birthdate"
         v-text="errors.birthdate[0]"></div>

    <laravel-select label="Genre"
                    name="gender"
                    v-model="gender"
                    :options="config.genders"
                    autocomplete="sex"
                    required></laravel-select>

    <laravel-input label="Numéro de téléphone"
                   class="mb-4"
                   name="phone"
                   v-model="phone"
                   type="tel"
                   autocomplete="tel"
                   placeholder="+33 6 69 69 69 69"
                   help-text="Promis, on en abusera pas !"
                   pattern="[\d+. -]+"
                   required></laravel-input>

    <button type="submit"
            class="btn btn-primary rounded-pill d-flex align-items-center mx-auto">
        <span>Vérification</span>

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
