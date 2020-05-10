<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserAvatarController extends Controller
{
    /**
     * Create a new UserAvatarController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image',
        ]);

        // Delete existing avatar from disk.
        Storage::disk('public')->delete($request->user()->getRawOriginal('avatar_path'));

        $request->user()->update([
            'avatar_path' => $request->file('avatar')->store('avatars', 'public'),
        ]);

        return Response('', 204);
    }
}
