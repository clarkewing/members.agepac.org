<?php

namespace App\Http\Controllers;

use App\Aircraft;
use App\Http\Requests\StoreOccupation;
use App\Occupation;
use App\Rules\ValidLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
     * @param  \App\Http\Requests\StoreOccupation  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOccupation $request)
    {
        $occupation = tap($request->user()->addExperience($request->all()))
            ->setLocation($request->input('location'));

        return Response::json($occupation, HttpResponse::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Occupation  $occupation
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Occupation $occupation)
    {
        $this->authorize('update', $occupation);

        $this->validateUpdate($request, $occupation);

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

    /**
     * Validates an update request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Occupation  $occupation
     * @return array
     */
    protected function validateUpdate(Request $request, Occupation $occupation): array
    {
        $validator = Validator::make($request->all(), [
            'position' => 'sometimes|required|string|max:255',
            'aircraft_id' => [
                'sometimes',
                'nullable',
                'int',
                Rule::exists(Aircraft::class, 'id'),
            ],
            'company' => 'sometimes|required|string|max:255',
            'location' => [
                'sometimes',
                'required',
                new ValidLocation,
            ],
            'status_code' => [
                'sometimes',
                'required',
                Rule::in(array_keys(Occupation::statusStrings())),
            ],
            'start_date' => [
                'sometimes',
                'required',
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'sometimes',
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'description' => 'sometimes|nullable|string|max:65535',
            'is_primary' => 'sometimes|boolean',
        ]);

        if ($request->has('end_date')) {
            $validator->addRules(['start_date' => 'before_or_equal:end_date']);
        } elseif (! is_null($occupation->end_date)) {
            $validator->addRules(['start_date' => 'before_or_equal:' . $occupation->end_date->toDateString()]);
        }

        if ($request->has('start_date')) {
            $validator->addRules(['end_date' => 'after_or_equal:start_date']);
        } else {
            $validator->addRules(['end_date' => 'after_or_equal:' . $occupation->start_date->toDateString()]);
        }

        $validator->sometimes('is_primary', Rule::in([false]), function ($payload) use ($occupation) {
            if ($payload->__isset('end_date')) {
                return true;
            }

            return ! is_null($occupation->end_date);
        });

        return $validator->validated();
    }
}