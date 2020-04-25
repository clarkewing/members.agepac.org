<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * Get top 5 trending threads.
     *
     * @return array
     */
    public function get(): array
    {
        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 4)); // Top 5 items
    }

    /**
     * Push a thread to the trending threads list.
     *
     * @param  \App\Thread $thread
     * @return void
     */
    public function push(Thread $thread): void
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path'  => $thread->path(),
        ]));
    }

    /**
     * Reset the trending threads list.
     *
     * @return void
     */
    public function reset(): void
    {
        Redis::del($this->cacheKey());
    }

    /**
     * Get the cache key for the redis database.
     *
     * @return string
     */
    protected function cacheKey(): string
    {
        return app()->environment('testing') ? 'testing_trending_threads' : 'trending_threads';
    }
}
