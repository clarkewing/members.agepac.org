<div>
    @foreach($poll->getResults() as $option)
        <div class="row align-items-center">
            <div class="col-md-3">
                @can('viewVotes', $poll)
                    <button type="button"
                            class="btn btn-link text-left p-0"
                            wire:click="showVoters({{ $option->id }})"
                    >
                        {{ $option->label }}
                    </button>
                @else
                    {{ $option->label }}
                @endcan
            </div>

            <div class="col-md-9">
                <div class="progress">
                    <div class="progress-bar"
                         style="width: {{ $option->votes_percent }}%; background: {{ $option-> color }};"
                    >
                        {{ $option->votes_count }} ({{ $option->votes_percent }}%)
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="d-flex mt-3">
        <button type="button" wire:click="showBallot" class="btn btn-outline-secondary">
            Voir mon bulletin
        </button>
    </div>
</div>

@includeWhen($modalOption, 'threads.poll.voters-modal')
