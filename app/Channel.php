<?php

namespace App;

use App\Thread;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the threads for the channel.
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
