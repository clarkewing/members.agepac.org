<div role="tabpanel"
     class="tab-pane fade {{ $active ? 'active show' : null }}">

    <h5 class="text-center mb-3">Quelques derniers détails...</h5>

    <form wire:submit.prevent="run">

        <div class="form-row mb-3">
            <legend class="col-form-label">Date de naissance</legend>

            <div class="form-group col-3 mb-0">
                <select class="form-control @error('birthdate_day') is-invalid @enderror"
                        id="birthdate_day"
                        wire:model.defer="birthdate_day"
                        autocomplete="bday-day"
                        required>

                    <option value="" disabled>Jour</option>

                    @foreach($listOfDays as $dayOption)
                        <option value="{{ $dayOption }}">
                            {{ $dayOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-5 mb-0">
                <select class="form-control @error('birthdate_month') is-invalid @enderror"
                        id="birthdate_month"
                        wire:model.defer="birthdate_month"
                        autocomplete="bday-month"
                        required>

                    <option value="" disabled>Mois</option>

                    @foreach($listOfMonths as $monthKey => $monthOption)
                        <option value="{{ $monthKey }}">
                            {{ $monthOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-4 mb-0">
                <select class="form-control @error('birthdate_year') is-invalid @enderror"
                        id="birthdate_year"
                        wire:model.defer="birthdate_year"
                        autocomplete="bday-year"
                        required>

                    <option value="" disabled>Année</option>

                    @foreach($listOfYears as $yearOption)
                        <option value="{{ $yearOption }}">
                            {{ $yearOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('birthdate')
                <div class="invalid-feedback d-block mt-n2 mb-3">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="gender">
                Genre
            </label>

            <select class="form-control @error('gender') is-invalid @enderror"
                    id="gender"
                    wire:model.defer="gender"
                    autocomplete="sex"
                    required>

                <option value="" disabled></option>

                @foreach(config('council.genders') as $genderKey => $genderOption)
                    <option value="{{ $genderKey }}">
                        {{ $genderOption }}
                    </option>
                @endforeach
            </select>

            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">
                Numéro de téléphone
            </label>

            <input type="tel"
                   class="form-control @error('phone') is-invalid @enderror"
                   id="phone"
                   wire:model.defer="phone"
                   autocomplete="tel"
                   placeholder="+33 6 69 69 69 69"
                   pattern="[\d+. -]+"
                   required>

            <small class="form-text text-muted"
                   id="phoneHelp"
            >
                Promis, on en abusera pas !
            </small>

            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary rounded-pill d-flex align-items-center mx-auto">
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
</div>
