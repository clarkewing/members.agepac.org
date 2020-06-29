<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Rules\ValidLocation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProfilesController extends Controller
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
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $profile
     * @return array|\Illuminate\View\View
     */
    public function show(Request $request, User $profile)
    {
        $data = [
            'profile' => $profile->load([
                'location',
                'experience',
                'education',
            ]),
            'activities' => Activity::feed($profile),
        ];

        if ($request->expectsJson()) {
            return $data;
        }

        return view('profiles.show', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $profile
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $profile)
    {
        $this->authorize('update', $profile);

        $request->validate([
            'flight_hours' => 'sometimes|nullable|integer|min:0|max:16777215',
            'location' => [
                'sometimes',
                'nullable',
                new ValidLocation,
            ],
            'bio' => 'sometimes|nullable|string|max:65535',
        ]);

        $profile->fill($request->input())->save();

        if ($request->has('location')) {
            $profile->setLocation($request->input('location'));
        }

        return Response::json($profile);
    }
}
