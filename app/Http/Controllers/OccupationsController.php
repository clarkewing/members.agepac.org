<?php

namespace App\Http\Controllers;

use App\Aircraft;
use App\Occupation;
use App\Rules\ValidLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

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

        if ($request->filled('location')) {
            $occupation->location->update($request->input('location'));
        }

        $occupation->update($request->all());

        return Response::json($occupation->fresh('location'));
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
