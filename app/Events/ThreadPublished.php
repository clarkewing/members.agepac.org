<?php

namespace App\Events;

use App\Thread;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Symfony\Contracts\EventDispatcher\Event;

class ThreadPublished extends Event
{
    use Dispatchable, SerializesModels;

    /**
     * \App\Thread.
     */
    public $thread;

    /**
     * Create a new event instance.
     *
     * @param  \App\Thread  $thread
     * @return void
     */
    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }
}
