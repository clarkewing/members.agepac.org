<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Poll extends Model
{
    public static $votesPrivacyValues = [
        0 => 'public',
        1 => 'private',
        2 => 'anonymous',
    ];

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
        'locked_at' => 'date:Y-m-d H:i:s',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['options'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['has_votes'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($poll) {
            if (in_array($poll->votes_privacy, static::$votesPrivacyValues, true)) {
                $poll->votes_privacy = array_flip(static::$votesPrivacyValues)[$poll->votes_privacy];
            }
        });
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
        return $this->hasManyThrough(
            PollVote::class,
            PollOption::class,
            'poll_id',
            'option_id'
        );
    }

    /**
     * Determine if the thread is locked.
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        if (is_null($this->locked_at)) {
            return false;
        }

        return $this->locked_at->isPast();
    }

    /**
     * Add an option to the poll.
     *
     * @param  array $option
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addOption(array $option)
    {
        return $this->options()->updateOrCreate(
            ['label' => $option['label']], $option
        );
    }

    /**
     * Add options to the poll.
     *
     * @param  array|null  $options
     * @return void
     */
    public function addOptions(?array $options)
    {
        if (! is_null($options)) {
            foreach ($options as $option) {
                $this->addOption($option);
            }
        }
    }

    /**
     * Sync options for the poll.
     *
     * @param  array|null  $options
     * @return void
     */
    public function syncOptions(?array $options)
    {
        $options = $options ?? [];

        $this->options()
            ->whereNotIn('label', Arr::pluck($options, 'label'))
            ->delete();

        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    /**
     * Cast a vote.
     *
     * @param  array  $vote
     * @param  \App\User|null  $user
     * @return void
     */
    public function castVote(array $vote, User $user = null)
    {
        $user_id = optional($user)->id ?? Auth::id();

        // Reset option votes.
        $this->votes()
            ->where('user_id', $user_id)
            ->delete();

        // Cast new option votes.
        PollVote::insert(array_map(function ($option_id) use ($user_id) {
            return compact('option_id', 'user_id');
        }, $vote));
    }

    /**
     * Gets whether the poll has received votes.
     *
     * @return bool
     */
    public function getHasVotesAttribute(): bool
    {
        return $this->votes()->exists();
    }

    /**
     * Get a user's vote.
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

    /**
     * Get the current user's vote.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVoteAttribute()
    {
        return $this->getVote();
    }

    /**
     * Determine if the user has voted.
     *
     * @param  \App\User|null  $user
     * @return bool
     */
    public function hasVoted(User $user = null): bool
    {
        return $this->votes()
            ->where('user_id', optional($user)->id ?? Auth::id())
            ->exists();
    }

    /**
     * Return the results of the poll.
     *
     * @param  bool  $withVotes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResults(bool $withVotes = false)
    {
        $totalVotes = $this->votes()->count() ?: 1; // If falsy value (ie. 0), use 1 to avoid division by zero

        if ($withVotes) {
            $this->load('options.voters');
        }

        return $this->options->map(function ($option) use ($totalVotes) {
            $option->votes_count = $option->votes()->count();
            $option->votes_percent = round($option->votes_count / $totalVotes * 100, 3);

            return $option;
        });
    }
}
