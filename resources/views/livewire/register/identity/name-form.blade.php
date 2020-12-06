<form wire:submit.prevent="findInvitation">

    <div class="form-group">
        <label for="name">
            Comment t’appelles-tu ?
        </label>

        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               wire:model.defer="name"
               autocomplete="name"
               placeholder="Marc Houalla"
               required
               autofocus>

        @error('name')
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
