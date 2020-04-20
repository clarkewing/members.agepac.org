<?php

namespace App;

use App\User;

class Reputation
{
    const THREAD_PUBLISHED = 10;
    const REPLY_POSTED = 2;
    const REPLY_FAVORITED = 5;
    const BEST_REPLY_AWARDED = 50;

    /**
     * Award the user a specific number of reputation points.
     *
     * @param  \App\User|null $user
     * @param  int $points
     * @return void
     */
    public static function award(?User $user, int $points): void
    {
        $user->increment('reputation', $points);
    }

    /**
     * Reduce the user's reputation by a specific number of points.
     *
     * @param  \App\User|null $user
     * @param  int $points
     * @return void
     */
    public static function reduce(?User $user, int $points): void
    {
        $user->decrement('reputation', $points);
    }
}
