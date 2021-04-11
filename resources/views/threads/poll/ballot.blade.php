<form wire:submit.prevent="castVote">
    @foreach($poll->options as $option)
        <div class="form-check mb-1">
            <input class="form-check-input"
                   type="checkbox"
                   wire:model.defer="state.vote"
                   name="poll_options"
                   id="poll_option_{{ $option->id }}" value="{{ $option->id }}"
                   @cannot('vote', $poll) disabled @endcannot
            >
            <label class="form-check-label" for="poll_option_{{ $option->id }}">{{ $option->label }}</label>
        </div>
    @endforeach

    @error('state.vote')
        <div class="d-block invalid-feedback">{{ $message }}</div>
    @enderror

    <div class="d-flex mt-3">
        <button
            type="submit"
            class="btn btn-success"
            @cannot('vote', $poll) disabled @endcannot
        >
            @can('vote', $poll)
                Soumettre
            @else
                Tu ne peux pas modifier ton vote
            @endcan
        </button>

        @can('viewResults', $poll)
            <button type="button" wire:click="showResults" class="btn btn-outline-secondary ml-2">
                Voir les résultats
            </button>
        @endcan
    </div>
</form>
