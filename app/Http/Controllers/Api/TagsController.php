<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Spatie\Tags\Tag;

class TagsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $type = null)
    {
        $request->validate([
            'query' => 'sometimes|string',
        ]);

        $searchQuery = Str::slug($request->input('query'));

        $tags = Tag
            ::when($type, function ($query, $type) {
                return $query->withType($type);
            })
            ->when($searchQuery, function ($query, $searchQuery) {
                // Search by slug because of difficulties with searching translatable fields.
                return $query->where('slug->fr', 'LIKE', "%{$searchQuery}%");
            });

        return Response::json($tags->get());
    }
}
