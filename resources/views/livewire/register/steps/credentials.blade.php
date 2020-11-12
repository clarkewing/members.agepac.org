<div role="tabpanel"
     class="tab-pane fade {{ $active ? 'active show' : null }}">

    <h5 class="text-center mb-3">Identifiants</h5>

    <form wire:submit.prevent="run">

        <div class="form-group">
            <label for="email">
                Adresse email
            </label>

            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   wire:model.defer="email"
                   autocomplete="email"
                   placeholder="choucroute_loveur@example.com"
                   required>

            <small class="form-text text-muted"
                   id="emailHelp"
            >
                Utilise ton adresse personnelle.
            </small>

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
                Choisis quelque chose de sûr et d'au moins 8 caractères.
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
