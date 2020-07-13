<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule as BaseRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidLocation implements BaseRule
{
    /**
     * @var \Illuminate\Support\Facades\Validator
     */
    protected $validator;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $baseRules = [
            'nullable',
            'string',
            'max:255',
        ];

        // First, validate the location is an array and has a type.
        $this->validator = Validator::make([$attribute => $value], [
            $attribute => 'array',

            "$attribute.type" => [
                'required',
                Rule::in(['country', 'city', 'address', 'busStop', 'trainStation', 'townhall', 'airport']),
            ],
        ]);

        // If this fails, stop validating.
        if (! $this->validator->passes()) {
            return $this->validator->passes();
        }

        // Otherwise, continue with the validation.
        $this->validator = Validator::make([$attribute => $value], [
            "$attribute.name" => array_merge($baseRules, [
                Rule::requiredIf(function () use ($value) {
                    return in_array($value['type'], ['busStop', 'trainStation', 'townhall', 'airport']);
                }),
            ]),

            "$attribute.street_line_1" => array_merge($baseRules, [
                Rule::requiredIf(function () use ($value) {
                    return in_array($value['type'], ['address']);
                }),
            ]),

            "$attribute.street_line_2" => $baseRules,

            "$attribute.municipality" => array_merge($baseRules, [
                Rule::requiredIf(function () use ($value) {
                    return in_array($value['type'], ['city', 'address']);
                }),
            ]),

            "$attribute.administrative_area" => $baseRules,

            "$attribute.sub_administrative_area" => $baseRules,

            "$attribute.postal_code" => $baseRules,

            "$attribute.country" => array_merge($baseRules, [
                Rule::requiredIf(function () use ($value) {
                    return in_array($value['type'], ['country', 'city', 'address']);
                }),
            ]),

            "$attribute.country_code" => [
                'nullable',
                'string',
                'size:2',
                Rule::requiredIf(function () use ($value) {
                    return in_array($value['type'], ['country', 'city', 'address']);
                }),
            ],
        ]);

        return $this->validator->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return array
     */
    public function message()
    {
        return $this->validator->errors();
    }
}
