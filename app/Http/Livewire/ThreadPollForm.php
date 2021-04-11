<?php

namespace App\Http\Livewire;

use App\Models\Poll;
use App\Models\Thread;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ThreadPollForm extends Component
{
    use AuthorizesRequests;

    /**
     * The component's state.
     */
    public array $state = [
        'title' => '',
        'options' => [
            ['label' => '', 'color' => '#1FBC9C'],
            ['label' => '', 'color' => '#E84B3C'],
        ],
        'votes_editable' => true,
        'max_votes' => 1,
        'votes_privacy' => 'public',
        'results_before_voting' => false,
        'locked_at' => null,
    ];

    public bool $maxVotesNull;

    public bool $lockedAtNull;

    /**
     * The thread model.
     */
    public Thread $thread;

    /**
     * The poll model.
     */
    public $poll;

    /**
     * The listeners for the component.
     */
    protected $listeners = [
        'openForm' => 'openForm',
        'pollDeleted' => '$refresh',
    ];

    public function mount(Thread $thread): void
    {
        $this->poll = $thread->poll;

        if (! is_null($this->poll)) {
            $this->state = $this->poll->only(['title', 'options', 'votes_editable', 'max_votes', 'votes_privacy', 'results_before_voting', 'locked_at']);
            $this->state['locked_at'] = optional($this->poll->locked_at)->toDateTimeString();
        }

        $this->maxVotesNull = $this->state['max_votes'] === null;
        $this->lockedAtNull = $this->state['locked_at'] === null;
    }

    public function updatedMaxVotesNull($value)
    {
        if ($value) {
            $this->state['max_votes'] = null;
        } else {
            $this->state['max_votes'] = 1;
        }
    }

    public function updatedLockedAtNull($value)
    {
        if ($value) {
            $this->state['locked_at'] = null;
        } else {
            $this->state['locked_at'] = now()
                ->addDays(7)
                ->setHour(23)
                ->setMinute(59)
                ->toDateTimeString();
        }
    }

    public function openForm()
    {
        $this->dispatchBrowserEvent('showForm');
    }

    public function addOption()
    {
        $this->state['options'][] = ['label' => '', 'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))];
    }

    public function removeOption(int $index)
    {
        unset($this->state['options'][$index]);

        $this->state['options'] = array_values($this->state['options']);

        if (count($this->state['options']) === 0) {
            $this->addOption();
        }
    }

    public function save()
    {
        $this->authorizeSave();

        $this->validate();

        if (is_null($this->poll)) {
            $this->poll = $this->thread->poll()->create($this->state);
        } else {
            $this->poll->fill($this->state)->save();
        }

        $this->poll->syncOptions($this->state['options']);

        $this->emit('pollUpdated');
    }

    public function render()
    {
        return view('threads.poll.form');
    }

    protected function rules()
    {
        return [
            'state.title' => ['required'],
            'state.options' => ['required', 'array', 'min:2'],
            'state.options.*.label' => ['required', 'max:255'],
            'state.options.*.color' => ['nullable', 'regex:/^#([A-F0-9]{6}|[A-F0-9]{3})$/i'],
            'state.votes_editable' => ['required', 'boolean'],
            'state.max_votes' => ['nullable', 'integer', 'min:1', 'max:' . count($this->state['options'])],
            'state.votes_privacy' => ['required', Rule::in(Poll::$votesPrivacyValues)],
            'state.results_before_voting' => ['required', 'boolean'],
            'state.locked_at' => ['nullable', 'date_format:Y-m-d H:i:s'],
        ];
    }

    protected function authorizeSave(): void
    {
        if (is_null($this->poll)) {
            $this->authorize('attachPoll', $this->thread);
        } else {
            $this->authorize('update', $this->poll);
        }
    }
}
