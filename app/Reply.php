<?php

namespace App;

use App\User;
use App\Thread;
use App\Favorite;
use App\RecordsActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favoritable, RecordsActivity;

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
     * Returns an array of users mentioned in body.
     *
     * @return \Illuminate\Support\Collection
     */
    public function mentionedUsers(): Collection
    {
        preg_match_all('/@([\w-]+)/', $this->body, $matches);

        return User::whereIn('name', $matches[1])->get();
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
     * Sets the body of the reply.
     *
     * @param  string $body
     * @return void
     */
    public function setBodyAttribute($body): void
    {
        $this->attributes['body'] = preg_replace('/@([\w-]+)/', '<a href="/profiles/$1">$0</a>', $body);
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
