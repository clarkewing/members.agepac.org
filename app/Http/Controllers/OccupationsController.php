<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOccupationRequest;
use App\Http\Requests\UpdateOccupationRequest;
use App\Occupation;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class OccupationsController extends Controller
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
     * @param  \App\Http\Requests\StoreOccupationRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOccupationRequest $request)
    {
        $occupation = tap($request->user()->profile->addExperience($request->all()))
            ->setLocation($request->input('location'));

        return Response::json($occupation, HttpResponse::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOccupationRequest  $request
     * @param  \App\Occupation  $occupation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOccupationRequest $request, Occupation $occupation)
    {
        $occupation->update($request->all());

        if ($request->filled('location')) {
            $occupation->setLocation($request->input('location'));
        }

        return Response::json($occupation->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Occupation  $occupation
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(Occupation $occupation)
    {
        $this->authorize('delete', $occupation);

        $occupation->delete();

        return Response::noContent();
    }
}
