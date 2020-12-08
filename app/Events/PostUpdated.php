<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Symfony\Contracts\EventDispatcher\Event;

class PostUpdated extends Event
{
    use Dispatchable, SerializesModels;

    /**
     * \App\Models\Post.
     */
    public $post;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Post $post
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
