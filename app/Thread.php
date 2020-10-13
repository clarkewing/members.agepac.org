<?php

namespace App;

use App\Events\ThreadPublished;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Thread extends Model
{
    use MentionsUsers, RecordsActivity, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'channel_id',
        'title',
        'slug',
        'best_post_id',
        'locked',
        'pinned',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['path'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['creator', 'channel'];
    protected $withCount = ['replies'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'locked' => 'boolean',
        'pinned' => 'boolean',
        'visits' => 'integer',
        'user_id' => 'integer',
    ];

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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($thread) {
            $thread->slug = $thread->title;
        });

        static::created(function ($thread) {
            $thread->creator->gainReputation('thread_published');
        });

        // Cascade deleting of thread.
        static::deleting(function ($thread) {
            $thread->posts->each->delete();

            $thread->creator->loseReputation('thread_published');
        });
    }

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ThreadPublished::class,
    ];

    /**
     * Returns the URL of the thread.
     *
     * @return string
     */
    public function path(): string
    {
        if (! $this->exists) {
            return '';
        }

        return route('threads.show', [$this->channel, $this]);
    }

    /**
     * Get the path for the thread.
     *
     * @return string
     */
    public function getPathAttribute(): string
    {
        return $this->path();
    }

    /**
     * Get the thread's posts.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the initiator post for the thread.
     */
    public function initiatorPost()
    {
        return $this->hasOne(Post::class)
            ->where('is_thread_initiator', true);
    }

    /**
     * Get the thread's replies.
     */
    public function replies()
    {
        return $this->posts()
            ->where('is_thread_initiator', false);
    }

    /**
     * Get the thread's best post.
     */
    public function bestPost()
    {
        return $this->hasOne(Post::class, 'id', 'best_post_id');
    }

    /**
     * Get the channel that the thread belongs to.
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class)->withoutGlobalScopes();
    }

    /**
     * Get the user that created the thread.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Add a post to the thread.
     *
     * @param  array $post
     * @return \App\Post
     */
    public function addPost(array $post)
    {
        $post = $this->posts()->create($post);

        return $post;
    }

    /**
     * Scope a query to filter.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param   $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * Creates a subscription to this thread for the given user.
     *
     * @param  int|null $userId
     * @return $this
     */
    public function subscribe(?int $userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?? Auth::id(),
        ]);

        return $this;
    }

    /**
     * Removes a subscription from this thread for the given user.
     *
     * @param  int|null $userId
     * @return $this
     */
    public function unsubscribe(?int $userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?? Auth::id())
            ->delete();

        return $this;
    }

    /**
     * Get the subscriptions for the thread.
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Get if the authenticated user is subscribed to the thread.
     *
     * @return bool
     */
    public function getIsSubscribedToAttribute(): bool
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->subscriptions()
            ->where('user_id', Auth::id())
            ->exists();
    }

    /**
     * Determine whether the thread has updates for the user.
     *
     * @param  \App\User $user
     * @return bool
     */
    public function hasUpdatesFor(User $user): bool
    {
        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    /**
     * Sets a unique slug for the thread.
     *
     * @param  string $title
     * @return void
     */
    public function setSlugAttribute($title): void
    {
        $slug = Str::slug($title);

        if (static::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . ($this->created_at ?? now())->timestamp;
        }

        $this->attributes['slug'] = $slug;
    }

    /**
     * Set the thread's best post.
     *
     * @param  \App\Post $post
     * @return void
     */
    public function markBestPost(Post $post): void
    {
        if ($this->hasBestPost()) {
            $this->bestPost->owner->loseReputation('best_post_awarded');
        }

        $this->update(['best_post_id' => $post->id]);

        $post->owner->gainReputation('best_post_awarded');
    }

    /**
     * Unset the thread's best post.
     *
     * @return void
     */
    public function unmarkBestPost(): void
    {
        if ($this->hasBestPost()) {
            $this->bestPost->owner->loseReputation('best_post_awarded');
        }

        $this->update(['best_post_id' => null]);
    }

    /**
     * Determine if the thread has a best post.
     *
     * @return bool
     */
    public function hasBestPost(): bool
    {
        return ! is_null($this->best_post_id);
    }
}
