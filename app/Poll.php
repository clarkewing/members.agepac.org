<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'thread_id',
        'title',
        'votes_editable',
        'votes_privacy',
        'max_votes',
        'results_before_voting',
        'locked_at',
    ];

    /**
     * Get the user that owns the poll.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the thread that the poll belongs to.
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the options of the poll.
     */
    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    /**
     * Get the votes of the poll.
     */
    public function votes()
    {
        return $this->hasManyThrough(PollVote::class, PollOption::class, 'poll_id', 'option_id');
    }

    /**
     * Add an option to the poll.
     *
     * @param  array $option
     * @return \App\PollOption
     */
    public function addOption(array $option)
    {
        $option = $this->options()->create($option);

        return $option;
    }
}
