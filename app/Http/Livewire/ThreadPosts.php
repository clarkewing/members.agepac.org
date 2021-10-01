<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ThreadPosts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshPosts' => '$refresh'];

    public $thread;

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $posts = $this->thread->posts();

        if (Auth::user()->can('viewDeleted', Post::class)) {
            $posts = $posts->withTrashed();
        }

        return view('threads.posts', [
            'posts' => $posts->paginate(3),
        ]);
    }
}
