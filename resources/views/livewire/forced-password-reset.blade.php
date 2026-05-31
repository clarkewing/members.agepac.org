<div>
    <h2 class="card-title text-center">Sécurisation du compte</h2>

    <div class="p-3">
        @if(! $verified)
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Incident de sécurité</h4>
                <p>
                    Notre site a récemment été la cible d’une cyberattaque. Par précaution, nous avons invalidé
                    l’ensemble des mots de passe des membres — y compris le tien — et tu dois en définir un nouveau pour
                    te reconnecter.
                </p>
                <hr>
                <p class="mb-0">
                    Si tu as des questions, n’hésite pas à <a href="mailto:bureau@agepac.org" class="alert-link">nous contacter</a>.
                </p>
            </div>

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

                    <small class="form-text text-muted" id="otpHelp">
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
            <p class="text-center text-muted mb-4">
                Choisis un nouveau mot de passe pour ton compte AGEPAC.
            </p>

            <form wire:submit.prevent="resetPassword">
                <div class="form-group">
                    <label for="password">
                        Nouveau mot de passe
                    </label>

                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           wire:model.defer="password"
                           autocomplete="new-password"
                           placeholder="Foy=Maison<3"
                           required
                           autofocus>

                    <small class="form-text text-muted" id="passwordHelp">
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
