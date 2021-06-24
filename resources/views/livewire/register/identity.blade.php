<div role="tabpanel"
     class="tab-pane fade {{ $active ? 'active show' : null }}">

    <h5 class="text-center mb-3">
        Dis-nous en plus...
    </h5>


    <form wire:submit.prevent="run">

        <div class="form-group">
            <label for="first_name">
                Prénom
            </label>

            <input type="text"
                   class="form-control @error('first_name') is-invalid @enderror"
                   id="first_name"
                   wire:model.defer="first_name"
                   autocomplete="given-name"
                   placeholder="Marc"
                   required>

            @error('first_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="last_name">
                Nom
            </label>

            <input type="text"
                   class="form-control @error('last_name') is-invalid @enderror"
                   id="last_name"
                   wire:model.defer="last_name"
                   autocomplete="family-name"
                   placeholder="Houalla"
                   required>

            @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

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

            @error('class_year')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

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
