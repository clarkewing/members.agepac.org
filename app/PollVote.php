<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'option_id',
    ];

    /**
     * Get the user that voted.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the poll.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }

    /**
     * Get the option chosen.
     */
    public function option()
    {
        return $this->belongsTo(PollOption::class, 'option_id');
    }
}
