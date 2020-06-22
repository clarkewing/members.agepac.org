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
     * @return array|\Illuminate\View\View
     */
    public function show(Request $request, User $user)
    {
        $profile = [
            'profileUser' => $user->load([
                'location',
                'experience',
                'education',
            ]),
            'activities' => Activity::feed($user),
        ];

        if ($request->expectsJson()) {
            return $profile;
        }

        return view('profiles.show', $profile);
    }
}
