<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Rule as BaseRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidLocation implements BaseRule
{
    /**
     * Validator factory used for slave validator creation.
     *
     * @var \Illuminate\Contracts\Validation\Factory
     */
    private $validatorFactory;

    /**
     * Validation error message bag from particular underlying validator.
     *
     * @var array
     */
    private $messageBag;

    /**
     * @param \Illuminate\Contracts\Validation\Factory|null $validatorFactory
     */
    public function __construct(Factory $validatorFactory = null)
    {
        if ($validatorFactory !== null) {
            $this->setValidatorFactory($validatorFactory);
        }
    }

    /**
     * @return \Illuminate\Contracts\Validation\Factory
     */
    public function getValidatorFactory(): Factory
    {
        if ($this->validatorFactory === null) {
            $this->validatorFactory = Validator::getFacadeRoot();
        }

        return $this->validatorFactory;
    }

    /**
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory
     * @return static
     */
    public function setValidatorFactory(Factory $validatorFactory): self
    {
        $this->validatorFactory = $validatorFactory;

        return $this;
    }

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
        $validator = $this->getValidatorFactory()->make([$attribute => $value], [
            $attribute => 'array',

            "$attribute.type" => [
                'required',
                Rule::in(['country', 'city', 'address', 'busStop', 'trainStation', 'townhall', 'airport']),
            ],
        ]);

        if ($validator->fails()) {
            $this->messageBag = $validator->getMessageBag()->all();

            return false;
        }

        // Otherwise, continue with the validation.
        $validator = $this->getValidatorFactory()->make([$attribute => $value], [
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

        if ($validator->fails()) {
            $this->messageBag = $validator->getMessageBag()->all();

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return array
     */
    public function message()
    {
        return $this->messageBag;
    }
}
