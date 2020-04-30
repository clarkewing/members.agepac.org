<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasReputation, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email', 'password', 'remember_token', 'email_verified_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    /**
     * Get the activity for the user.
     */
    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the threads for the user.
     */
    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    /**
     * Get the latest reply posted by the user.
     */
    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    /** Determine if user is administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return in_array($this->name, [
            'JohnDoe',
            'JaneDoe',
        ]);
    }

    /**
     * Get the cache key for visited threads.
     *
     * @param  \App\Thread $thread
     * @return string
     */
    public function read(Thread $thread)
    {
        Cache::forever(
            $this->visitedThreadCacheKey($thread),
            now()
        );
    }

    /**
     * Get the path of the User's avatar.
     *
     * @param  string $avatar
     * @return string
     */
    public function getAvatarPathAttribute($avatar): string
    {
        return $avatar
            ? Storage::url($avatar)
            : asset('images/avatars/default.jpg');
    }

    /**
     * Get the cache key for visited threads.
     *
     * @param  \App\Thread $thread
     * @return string
     */
    public function visitedThreadCacheKey(Thread $thread)
    {
        return sprintf('users.%s.visits.%s', $this->id, $thread->id);
    }
}
