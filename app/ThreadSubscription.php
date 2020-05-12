<?php

namespace App;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the thread that the subscription references.
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Notify the associated user.
     *
     * @param  \App\Post $post
     */
    public function notify(Post $post)
    {
        $this->user->notify(new ThreadWasUpdated($this->thread, $post));
    }
}
