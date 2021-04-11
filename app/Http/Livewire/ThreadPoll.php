<?php

namespace App\Http\Livewire;

use App\Models\Poll;
use App\Models\Thread;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ThreadPoll extends Component
{
    use AuthorizesRequests;

    /**
     * The component's state.
     */
    public array $state = [];

    /**
     * The currently displayed panel.
     */
    public string $panel = 'ballot';

    /**
     * The option detailed in the modal.
     */
    public $modalOption = null;

    /**
     * The poll model.
     */
    public ?Poll $poll;

    /**
     * The listeners for the component.
     */
    protected $listeners = ['pollUpdated' => '$refresh'];

    public function mount(Thread $thread): void
    {
        if (is_null($this->poll = $thread->poll)) {
            return;
        }

        $this->state['vote'] = array_map('strval', $this->poll->vote->pluck('option_id')->toArray());

        if ($this->poll->hasVoted()) {
            $this->showResults();
        }
    }

    public function castVote()
    {
        $this->authorize('vote', $this->poll);

        $this->validate();

        $this->poll->castVote($this->state['vote']);

        $this->showResults();
    }

    public function showBallot()
    {
        $this->panel = 'ballot';
    }

    public function showResults()
    {
        $this->authorize('viewResults', $this->poll);

        $this->panel = 'results';
    }

    public function showVoters(int $optionId)
    {
        $this->authorize('viewVotes', $this->poll);

        $this->modalOption = $this->poll->options->firstWhere('id', $optionId);

        $this->dispatchBrowserEvent('showVoters');
    }

    public function hideVoters()
    {
        $this->modalOption = null;
    }

    public function delete()
    {
        $this->authorize('delete', $this->poll);

        $this->poll->delete();

        $this->poll = null;

        $this->emit('pollDeleted');
    }

    public function render()
    {
        return view('threads.poll');
    }

    protected function rules()
    {
        $rules = [
            'state.vote' => ['array'],
            'state.vote.*' => ['integer', Rule::in($this->poll->options->pluck('id'))],
        ];

        if (! is_null($this->poll->max_votes)) {
            $rules['state.vote'][] = "max:{$this->poll->max_votes}";
        }

        return $rules;
    }

    protected $messages = [
        'state.vote.max' => 'Un maximum de :max options peuvent être sélectionnées.',
    ];
}
