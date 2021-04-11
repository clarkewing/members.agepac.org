<?php

namespace App\Http\Livewire;

use App\Models\Thread;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ThreadHeader extends Component
{
    use AuthorizesRequests;

    /**
     * Whether the user is editing the thread.
     */
    public bool $editing = false;

    /**
     * The component's state.
     */
    public array $state = [];

    /**
     * The thread model.
     */
    public Thread $thread;

    /**
     * The component's validation rules.
     */
    protected array $rules = [
        'state.title' => 'required',
    ];

    /**
     * Prepare the component.
     */
    public function mount(Thread $thread): void
    {
        $this->thread = $thread;

        $this->resetState();
    }

    /**
     * Update the thread.
     */
    public function update()
    {
        $this->authorize('update', $this->thread);

        $this->validate();

        $this->thread->update($this->state);

        $this->resetState();
    }

    /**
     * Toggle locking/unlocking the thread.
     */
    public function toggleLock()
    {
        $this->authorize(
            $this->thread->locked ? 'unlock' : 'lock',
            $this->thread
        );

        $this->thread->update(['locked' => ! $this->thread->locked]);
    }

    /**
     * Toggle pinning/unpinning the thread.
     */
    public function togglePin()
    {
        $this->authorize(
            $this->thread->pinned ? 'unpin' : 'pin',
            $this->thread
        );

        $this->thread->update(['pinned' => ! $this->thread->pinned]);
    }

    /**
     * Toggle the thread subscription.
     */
    public function toggleSubscription()
    {
        if ($this->thread->isSubscribedTo) {
            $this->thread->unsubscribe();
        } else {
            $this->thread->subscribe();
        }
    }

    /**
     * Reset the state.
     */
    public function resetState()
    {
        $this->editing = false;

        $this->state = $this->thread->only([
            'title',
        ]);
    }

    public function render()
    {
        return view('threads.header');
    }
}
