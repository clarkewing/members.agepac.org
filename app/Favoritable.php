<?php

namespace App;

use App\Favorite;
use App\Reputation;
use Illuminate\Support\Facades\Auth;

trait Favoritable
{
    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    public function initializeFavoritable()
    {
        $this->with[] = 'favorites';
        $this->withCount[] = 'favorites';
        $this->appends[] = 'is_favorited';
    }

    /**
     * Get all of the reply's favorites.
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Mark the reply as favorited by the given user.
     * If no userId is passed, authenticated user is used.
     *
     * @param  int|null $userId
     * @return \App\Favorite|void
     */
    public function favorite(int $userId = null)
    {
        $attributes = ['user_id' => $userId ?? Auth::id()];

        if (! $this->favorites()->where($attributes)->exists()) {
            Reputation::award($this->owner, Reputation::REPLY_FAVORITED);

            return $this->favorites()->create($attributes);
        }
    }

    /**
     * Remove the favorite from the given user.
     * If no userId is passed, authenticated user is used.
     *
     * @param  int|null $userId
     * @return void
     */
    public function unfavorite(int $userId = null): void
    {
        $attributes = ['user_id' => $userId ?? Auth::id()];

        $this->favorites()->where($attributes)->get()->each->delete();

        Reputation::reduce($this->owner, Reputation::REPLY_FAVORITED);
    }

    /**
     * Returns whether the given user has favorited this reply.
     * If no userId is passed, authenticated user is used.
     *
     * @param  int|null $userId
     * @return bool
     */
    public function isFavorited(int $userId = null): bool
    {
        return $this->favorites->where('user_id', $userId ?? Auth::id())->count();
    }

    /**
     * Get if the favoritable has been favorited by the current user.
     *
     * @return bool
     */
    public function getIsFavoritedAttribute($value)
    {
        return $this->isFavorited();
    }
}
