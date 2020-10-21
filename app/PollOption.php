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

    /**
     * Get the votes of the poll.
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class, 'option_id');
    }

    /**
     * Add a vote for the option.
     *
     * @param  array $vote
     * @return \App\PollVote
     */
    public function addVote(array $vote)
    {
        $vote = $this->votes()->create($vote);

        return $vote;
    }
}
