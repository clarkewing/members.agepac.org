<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display the search results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if ($request->expectsJson()) {
            return Thread::search($request->query('query'))->paginate(25);
        }

        return view('threads.search');
    }
}
