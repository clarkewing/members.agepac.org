<?php

namespace App\Events;

use App\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Symfony\Contracts\EventDispatcher\Event;

class PostUpdated extends Event
{
    use Dispatchable, SerializesModels;

    /**
     * \App\Post.
     */
    public $post;

    /**
     * Create a new event instance.
     *
     * @param  \App\Post $post
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
