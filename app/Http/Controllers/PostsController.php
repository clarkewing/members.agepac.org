<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('members-area');
        $this->middleware('verified:threads,Tu dois vérifier ton adresse email avant de pouvoir poster.')
            ->only(['store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $channelSlug
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, string $channelSlug, Thread $thread)
    {
        $this->authorize('view', $thread->channel);

        $posts = $thread->posts();

        if ($request->user()->can('viewDeleted', Post::class)) {
            $posts = $posts->withTrashed();
        }

        return $posts->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreatePostRequest  $request
     * @param  string  $channelSlug
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CreatePostRequest $request, string $channelSlug, Thread $thread)
    {
        $this->authorize('view', $thread->channel);

        if ($thread->locked) {
            return Response::make('Thread is locked.', 422);
        }

        return Response::json(
            $thread->addPost([
                'body' => $request->input('body'),
                'user_id' => Auth::id(),
            ])->load('owner'),
            HttpResponse::HTTP_CREATED
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Post $post)
    {
        if ($request->has('deleted_at') && $request->input('deleted_at') === null) {
            return $this->restore($request, $post);
        }

        $this->authorize('update', $post);

        $request->validate(['body' => 'required']);

        $post->fill($request->only('body'))->save();

        return Response::json($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        if ($request->expectsJson()) {
            return Response::json(['status' => 'Post deleted.']);
        }

        return back()
            ->with('flash', 'The post was deleted.');
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, Post $post)
    {
        $this->authorize('restore', $post);

        $post->restore();

        if ($request->expectsJson()) {
            return Response::json($post);
        }

        return back()
            ->with('flash', 'The post was restored.');
    }
}
