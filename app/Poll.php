<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'votes_editable' => 'boolean',
        'results_before_voting' => 'boolean',
        'max_votes' => 'integer',
        'votes_privacy' => 'integer',
        'locked_at' => 'date',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['options'];

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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addOption(array $option)
    {
        return $this->options()->create($option);
    }

    /**
     * Add options to the poll.
     *
     * @param  array  $options
     * @return void
     */
    public function addOptions(array $options)
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    /**
     * Cast a vote.
     *
     * @param  array  $vote
     * @return void
     */
    public function castVote(array $vote)
    {
        // Reset option votes.
        $this->votes()->where('user_id', $user_id = Auth::id())->delete();

        // Cast new option votes.
        PollVote::insert(array_map(function ($option_id) use ($user_id) {
            return compact('option_id', 'user_id');
        }, $vote));
    }

    /**
     * Get the user's vote.
     *
     * @param  \App\User|null  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVote(User $user = null)
    {
        return $this->votes()
            ->where('user_id', optional($user)->id ?? Auth::id())
            ->with('option')
            ->get();
    }
}
