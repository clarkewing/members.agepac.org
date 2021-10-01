<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ThreadNewPost extends Component
{
    public $thread;

    public bool $editing = false;

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('threads.new-post');
    }
}
