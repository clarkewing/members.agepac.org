<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class BestPostsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Post  $post
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Post $post, Request $request)
    {
        $this->authorize('update', $post->thread);

        $post->thread->markBestPost($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
