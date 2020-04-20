<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Trending;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display the search results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Trending $trending
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Trending $trending)
    {
        if ($request->expectsJson()) {
            return Thread::search($request->query('q'))->paginate(25);
        }

        return view('threads.search', [
            'trending' => $trending->get(),
        ]);
    }
}
