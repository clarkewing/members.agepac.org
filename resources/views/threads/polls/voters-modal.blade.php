<div class="modal fade" tabindex="-1" role="dialog" id="votersModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ $modalOption->label }}
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                >
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                @unless($modalOption->voters->isEmpty())
                    <ul>
                        @foreach($modalOption->voters as $voter)
                            <li>
                                <a href="{{ route('profiles.show', $voter) }}">
                                    {{ $voter->first_name }} {{ $voter->last_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center">
                        Aucun vote pour cette option.
                    </div>
                @endunless
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('showVoters', () => {
            $('#votersModal').modal('show');
        });
    </script>
@endpush
