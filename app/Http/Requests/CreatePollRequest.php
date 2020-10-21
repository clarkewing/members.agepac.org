<?php

namespace App\Http\Requests;

use App\Poll;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreatePollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('attachPoll', $this->thread);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'options' => 'required|array|min:2',
            'options.*.label' => 'required',
            'options.*.color' => ['nullable', 'regex:/^#([A-F0-9]{6}|[A-F0-9]{3})$/i'],
            'votes_editable' => 'required|boolean',
            'max_votes' => 'nullable|integer|min:1',
            'votes_privacy' => 'required|digits_between:0,2',
            'results_before_voting' => 'required|boolean',
            'locked_at' => 'date',
        ];
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
            $validator->addRules(['max_votes' => 'max:' . count($this->input('options'))]);
        }
    }
}
