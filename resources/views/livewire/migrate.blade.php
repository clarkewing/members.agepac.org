<div>
    <h2 class="card-title text-center">Migration de tes données</h2>

    <div class="p-3">
        @if(! $verified)
            <h5 class="text-center">Vérification</h5>

            <p class="text-center text-muted mb-4">
                Afin de confirmer ton identité, nous t’avons envoyé un email contenant un code à usage unique.
            </p>

            <form wire:submit.prevent="verify">
                <div class="form-group text-center">
                    <label for="token">
                        Saisis le code à 6 chiffres
                    </label>

                    <input type="text"
                           class="form-control text-center w-50 mx-auto @error('token') is-invalid @enderror"
                           id="token"
                           wire:model.defer="token"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           required
                           pattern="[0-9]*"
                           autofocus>

                    <small class="form-text text-muted"
                           id="otpHelp"
                    >
                        @if($resent)
                            Le code a été renvoyé à ton adresse email.
                        @else
                            Tu n’as pas reçu de code ?
                            <a href="#" wire:click.prevent="resendToken" class="text-muted">
                                Le renvoyer
                            </a>
                        @endif
                    </small>

                    @error('token')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary rounded-pill d-flex align-items-center mx-auto">
                    <span>Vérifier</span>

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
        @else
            <h5 class="text-center">Bonjour {{ $this->user->name }} !</h5>

            <p class="text-center text-muted mb-4">
                Voilà {{ $this->user->created_at->longAbsoluteDiffForHumans() }} que tu as rejoint l'AGEPAC !<br>
                Profitons de cette migration pour mettre à jour tes informations.
            </p>

            <form wire:submit.prevent="saveUser">
                @if($this->mustSetClass)
                    <div class="form-group">
                        <label for="class_course">
                            Cursus
                        </label>

                        <select class="form-control @error('class_course') is-invalid @enderror"
                                id="class_course"
                                wire:model.defer="class_course"
                                required>

                            <option value="" disabled></option>

                            @foreach(config('council.courses') as $courseOption)
                                <option value="{{ $courseOption }}">
                                    {{ $courseOption }}
                                </option>
                            @endforeach
                        </select>

                        <small class="form-text text-muted"
                               id="class_courseHelp"
                        >
                            Les EPL/S sont ab-initio, les EPL/U sont ceux entrés avec un ATPL théorique,
                            et les EPL/P ceux entrés avec un CPL pratique.
                        </small>

                        @error('class_course')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="class_year">
                            Promotion
                        </label>

                        <input type="number"
                               min="1900" max="2199" step="1"
                               class="form-control @error('class_year') is-invalid @enderror"
                               id="class_year"
                               wire:model.defer="class_year"
                               required>

                        <small class="form-text text-muted"
                               id="class_yearHelp"
                        >
                            L'année de promotion correspond à ton année d'entrée dans le cursus EPL.
                        </small>

                        @error('class_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="form-group">
                    <label for="email">
                        Adresse email
                    </label>

                    <input type="email"
                           class="form-control"
                           id="email"
                           value="{{ $user->email }}"
                           disabled>

                    <small class="form-text text-muted"
                           id="emailHelp"
                    >
                        Tu pourras modifier ton adresse email une fois connecté.
                    </small>
                </div>

                <div class="form-group">
                    <label for="password">
                        Mot de passe
                    </label>

                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           wire:model.defer="password"
                           autocomplete="new-password"
                           placeholder="Foy=Maison<3"
                           required>

                    <small class="form-text text-muted"
                           id="passwordHelp"
                    >
                        Choisis quelque chose de sûr et d’au moins 8 caractères.
                    </small>

                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="password_confirmation">
                        Confirmation
                    </label>

                    <input type="password"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           id="password_confirmation"
                           wire:model.defer="password_confirmation"
                           autocomplete="new-password"
                           required>

                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

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
                    <span>Valider</span>

                    <svg class="bi flaticon-takeoff ml-2" width="1em" height="1em" viewBox="0 0 12 12"
                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M1.2 8.2c-.1.1-.1.2-.1.3.1.1.2.2.4.2l4-1.3-.8 1.2c-.1.1 0 .2 0 .3.1.1.2.1.3 0l.8-.4h.1L8.2 6l2.9-1.5c1.1-.6 1-1 .9-1.2 0-.1-.2-.2-.4-.3-.3-.1-.6-.1-1 0h-.3c-.3 0-.5 0-1.6.5L3 6.6.8 5.5H.5l-.3.2c-.1 0-.2.1-.2.2s0 .1.1.2L1.6 8l-.4.2z"/>
                    </svg>
                </button>
            </form>
        @endif
    </div>
</div>
