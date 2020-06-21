<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user)
    {
        $profile = [
            'profileUser' => $user->load('location'),
            'activities' => Activity::feed($user),
        ];

        if ($request->expectsJson()) {
            return $profile;
        }

        return view('profiles.show', $profile);
    }
}
