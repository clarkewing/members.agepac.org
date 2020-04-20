<?php

namespace App\Filters;

use App\User;
use App\Reply;
use Illuminate\Http\Request;

class ThreadFilters extends Filters
{
    /**
     * The filters that are applied.
     *
     * @var array
     */
    protected $filters = [
        'by',
        'popular',
        'unanswered',
    ];

    /**
     * Filter the query by a given username.
     *
     * @param  string $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function by(string $username)
    {
        $user = User::where('name', $username)->firstOrFail();
        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter the query according to most popular threads.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function popular()
    {
        return $this->builder->orderBy('replies_count', 'desc');
    }

    /**
     * Filter the query according to unanswered threads.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function unanswered()
    {
        return $this->builder->whereNotIn('id', function ($query) {
            $query->select('thread_id')
                ->from((new Reply)->getTable());
        });

        // Fails testing with SQLite:
        // return $this->builder->having('replies_count', 0);
    }
}
