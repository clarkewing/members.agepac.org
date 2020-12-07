<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UpdateOccupationRequest extends StoreOccupationRequest
{
    /**
     * @var \App\Occupation
     */
    protected $occupation;

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->occupation = $this->route('occupation');
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->occupation);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->applySometimes(array_merge(parent::rules(), [
            'end_date' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'is_primary' => [
                'boolean',
            ],
        ]));
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        if ($this->missing('end_date') && ! is_null($this->occupation->end_date)) {
            $validator->addRules(['start_date' => 'before_or_equal:' . $this->occupation->end_date->toDateString()]);
        }

        if ($this->has('start_date')) {
            $validator->addRules(['end_date' => 'after_or_equal:start_date']);
        } else {
            $validator->addRules(['end_date' => 'after_or_equal:' . $this->occupation->start_date->toDateString()]);
        }

        // Occupation cannot be set as primary if end_date is in past (hence not null).
        if ($this->filled('end_date') || ! is_null($this->occupation->end_date)) {
            $validator->addRules(['is_primary' => Rule::in([false])]);
        }
    }

    /**
     * Apply 'sometimes' validation rule to rules.
     *
     * @param  $rules
     * @return array
     */
    protected function applySometimes($rules): array
    {
        return array_map(function ($conditions) {
            return Arr::prepend($conditions, 'sometimes');
        }, $rules);
    }
}
