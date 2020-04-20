<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Visits
{
    protected $thread;

    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    /**
     * Get number of visits to the thread.
     *
     * @return int
     */
    public function count(): int
    {
        return Redis::get($this->cacheKey()) ?? 0;
    }

    /**
     * Record a visit to the thread.
     *
     * @return self
     */
    public function record(): self
    {
        Redis::incr($this->cacheKey());

        return $this;
    }

    /**
     * Reset number of visits to the thread.
     *
     * @return self
     */
    public function reset(): self
    {
        Redis::del($this->cacheKey());

        return $this;
    }

    protected function cacheKey()
    {
        return "threads.{$this->thread->id}.visits";
    }
}
