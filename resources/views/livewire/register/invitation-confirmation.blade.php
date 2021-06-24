<div role="tabpanel"
     class="tab-pane fade {{ $active ? 'active show' : null }}">

    <h5 class="text-center mb-3">
        Trouvé !
    </h5>

    <div>
        <dl class="mb-4">
            <dt>Prénom</dt>
            <dd>{{ $first_name }}</dd>

            <dt>Nom</dt>
            <dd>{{ $last_name }}</dd>

            <dt>Promotion</dt>
            <dd>{{ $class_course }} {{ $class_year }}</dd>
        </dl>

        <button type="button"
                class="btn btn-primary rounded-pill d-flex align-items-center mx-auto mb-2"
                wire:click="run">
            <span>C’est bien moi</span>

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
                wire:click="resetIdentity">
            <span>Ce n’est pas moi</span>
        </button>
    </div>

</div>
