<?php

namespace App;

use App\Models\Thread;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Trending
{
    /**
     * Get top 5 trending threads.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return Cache::get($this->cacheKey(), collect())
            ->sortByDesc('score')
            ->slice(0, 5)
            ->values();
    }

    /**
     * Push a thread to the trending threads list.
     *
     * @param  \App\Models\Thread  $thread
     * @param  int  $increment
     * @return void
     */
    public function push(Thread $thread, int $increment = 1): void
    {
        $trending = Cache::get($this->cacheKey(), collect());

        $trending[$thread->id] = (object) [
            'score' => optional($trending->get($thread->id))->score + $increment,
            'title' => $thread->title,
            'path' => $thread->path(),
        ];

        Cache::forever($this->cacheKey(), $trending);
    }

    /**
     * Get the trending score of the given thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return int
     */
    public function score(Thread $thread): int
    {
        $trending = Cache::get($this->cacheKey(), collect());

        if (! isset($trending[$thread->id])) {
            return 0;
        }

        return $trending[$thread->id]->score;
    }

    /**
     * Reset the trending threads list.
     *
     * @return void
     */
    public function reset(): void
    {
        Cache::forget($this->cacheKey());
    }

    /**
     * Get the cache key name.
     *
     * @return string
     */
    protected function cacheKey(): string
    {
        return 'trending_threads';
    }
}
