<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class UpdatePollRequest extends StorePollRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', $this->poll);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->applySometimes(array_merge(parent::rules()));
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        if ($validator->passes()) {
            if ($this->has('options')) {
                $validator->addRules(['max_votes' => 'max:' . count($this->input('options'))]);
            } else {
                $validator->addRules(['max_votes' => 'max:' . $this->poll->options()->count()]);
            }
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
