<?php

namespace App;

use App\Events\ReplyPosted;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class Reply extends Model
{
    use Favoritable, MentionsUsers, RecordsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'thread_id',
        'user_id',
        'body',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_best'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['owner'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['thread'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($reply) {
            $reply->owner->gainReputation('reply_posted');
        });

        static::deleting(function ($reply) {
            $reply->owner->loseReputation('reply_posted');
        });
    }

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ReplyPosted::class,
    ];

    /**
     * Get the user that owns the reply.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the thread that the reply belongs to.
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Determines whether the reply was just published.
     *
     * @return bool
     */
    public function wasJustPublished(): bool
    {
        return $this->created_at->greaterThan(now()->subMinute());
    }

    /**
     * Returns the URL of the reply.
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    /**
     * Determines whether the reply is marked as the best one on its thread.
     *
     * @return bool
     */
    public function isBest(): bool
    {
        return $this->thread->best_reply_id == $this->id;
    }

    /**
     * Get the best reply flag for the reply.
     *
     * @return bool
     */
    public function getIsBestAttribute(): bool
    {
        return $this->isBest();
    }

    /**
     * Get the sanitized body.
     *
     * @param  string  $value
     * @return string
     */
    public function getBodyAttribute($value)
    {
        return Purify::clean($value);
    }
}
