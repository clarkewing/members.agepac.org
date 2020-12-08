<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreOccupationRequest;
use App\Http\Requests\UpdateOccupationRequest;
use App\Models\Occupation;
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
        $company = Company::firstOrCreate($request->input('company'), [
            'type_code' => $request->has('aircraft_id')
                ? Company::AIRLINE
                : Company::OTHER_BUSINESS,
        ]);

        $occupation = $request->user()->profile->addExperience(
            $request->all() + ['company_id' => $company->id]
        );

        $occupation->setLocation($request->input('location'));

        return Response::json($occupation->fresh(), HttpResponse::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOccupationRequest  $request
     * @param  \App\Models\Occupation  $occupation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOccupationRequest $request, Occupation $occupation)
    {
        if ($request->filled('company')) {
            $occupation->company()->associate(Company::firstOrCreate($request->input('company'), [
                'type_code' => $request->has('aircraft_id')
                    ? Company::AIRLINE
                    : Company::OTHER_BUSINESS,
            ]));
        }

        $occupation->update($request->all());

        if ($request->filled('location')) {
            $occupation->setLocation($request->input('location'));
        }

        return Response::json($occupation->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Occupation  $occupation
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
