<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Profile;
use App\Rules\ValidLocation;
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
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            if ($request->has('query')) {
                return Profile::search($request->query('query'))
                    ->paginate(25);
            }

            return Profile::paginate(25);
        }

        return view('profiles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return array|\Illuminate\View\View
     */
    public function show(Request $request, Profile $profile)
    {
        $data = [
            'profile' => $profile,
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
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Profile $profile)
    {
        $this->authorize('update', $profile);

        $request->validate([
            'mentorship_tags' => 'sometimes|nullable|array',
            'flight_hours' => 'sometimes|nullable|integer|min:0|max:16777215',
            'location' => [
                'sometimes',
                'nullable',
                new ValidLocation,
            ],
            'bio' => 'sometimes|nullable|string|max:65535',
        ]);

        if ($request->has('location')) {
            $profile->setLocation($request->input('location'));
        }

        if ($request->has('mentorship_tags')) {
            $profile->syncTagsWithType($request->input('mentorship_tags'), 'mentorship');
        }

        $profile->update($request->all());

        return Response::json($profile->fresh());
    }
}
