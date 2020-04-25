<?php

namespace App\Events;

use App\Reply;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThreadReceivedNewReply
{
    use Dispatchable, SerializesModels;

    /**
     * \App\Reply.
     */
    public $reply;

    /**
     * Create a new event instance.
     *
     * @param  \App\Reply $reply
     * @return void
     */
    public function __construct(Reply $reply)
    {
        $this->reply = $reply;
    }
}
