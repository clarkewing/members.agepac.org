<?php

namespace App\Filters;

use App\Post;
use App\User;
use Illuminate\Support\Facades\View;

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
        $user = User::where('username', $username)->firstOrFail();

        View::share(['threadsUser' => $user]);

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter the query according to most popular threads.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function popular()
    {
        return $this->builder->orderBy('posts_count', 'desc');
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
                ->from((new Post)->getTable());
        });

        // Fails testing with SQLite:
        // return $this->builder->having('posts_count', 0);
    }
}
