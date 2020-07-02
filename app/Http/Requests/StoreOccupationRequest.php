<?php

namespace App\Http\Requests;

use App\Aircraft;
use App\Occupation;
use App\Rules\ValidLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOccupationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'position' => [
                'required',
                'string',
                'max:255',
            ],
            'aircraft_id' => [
                'nullable',
                'int',
                Rule::exists(Aircraft::class, 'id'),
            ],
            'company' => [
                'required',
                'string',
                'max:255',
            ],
            'location' => [
                'required',
                new ValidLocation,
            ],
            'status_code' => [
                'required',
                Rule::in(array_keys(Occupation::statusStrings())),
            ],
            'start_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:end_date',
            ],
            'end_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
                'before_or_equal:today',
            ],
            'description' => [
                'nullable',
                'string',
                'max:65535',
            ],
        ];
    }
}
