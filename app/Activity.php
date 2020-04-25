<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Activity extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Return a user's activity feed.
     *
     * @param  \App\User $user
     * @param  int $take
     * @return \Illuminate\Support\Collection
     */
    public static function feed(User $user, int $take = 50): Collection
    {
        return static::where('user_id', $user->id)
            ->with('subject')
            ->latest()
            ->take($take)
            ->get()
            ->groupBy(function ($activity) {
                return $activity->created_at->format('Y-m-d');
            });
    }

    /**
     * Get the activity's subject.
     */
    public function subject()
    {
        return $this->morphTo();
    }
}
