<?php

namespace App\Http\Livewire\Thread;

use Livewire\Component;

class Post extends Component
{
    protected $listeners = ['refreshPosts' => '$refresh'];

    public $post;

    public bool $editing = false;

    public bool $showDeleted = false;

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('threads.post');
    }

    public function toggleFavorite()
    {
        if ($this->post->is_favorited) {
            $this->post->unfavorite();

            return;
        }

        $this->post->favorite();
    }

    public function toggleBestPost()
    {
        if ($this->post->is_best) {
            $this->post->thread->unmarkBestPost();
        } else {
            $this->post->thread->markBestPost($this->post);
        }

        $this->emit('refreshPosts');
    }

    public function restore()
    {
        $this->post->restore();

        $this->emit('$refresh');
    }
}
