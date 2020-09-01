<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'poll_id',
        'label',
        'color',
    ];

    /**
     * Get the poll.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }
}
