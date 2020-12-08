<?php

namespace App\Http\Controllers;

use App\Models\Post;

class FavoritesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function store(Post $post)
    {
        $post->favorite();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function destroy(Post $post): void
    {
        $post->unfavorite();
    }
}
