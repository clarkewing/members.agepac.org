<?php

namespace App;

use App\Events\ReplyPosted;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
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
        static::created(function ($post) {
            $post->owner->gainReputation('reply_posted');
        });

        static::deleting(function ($post) {
            $post->owner->loseReputation('reply_posted');
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
     * Get the user that owns the post.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the thread that the post belongs to.
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Determines whether the post was just published.
     *
     * @return bool
     */
    public function wasJustPublished(): bool
    {
        return $this->created_at->greaterThan(now()->subMinute());
    }

    /**
     * Returns the URL of the post.
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path() . "#post-{$this->id}";
    }

    /**
     * Determines whether the post is marked as the best one on its thread.
     *
     * @return bool
     */
    public function isBest(): bool
    {
        return $this->thread->best_post_id == $this->id;
    }

    /**
     * Get the best post flag for the post.
     *
     * @return bool
     */
    public function getIsBestAttribute(): bool
    {
        return $this->isBest();
    }
}
