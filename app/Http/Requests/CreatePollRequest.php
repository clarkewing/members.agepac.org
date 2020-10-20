<?php

namespace App\Http\Requests;

use App\Exceptions\ThrottleException;
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
        return Gate::allows('create', Poll::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $option_labels = $this->request->get('option_labels');
        $rules = [
            'title' => 'required',
            'votes_editable' => 'required|boolean',
            'max_votes' => 'nullable|digits_between:1,' . count($option_labels),
            'votes_privacy' => 'required|digits_between:0,2',
            'results_before_voting' => 'required|boolean',
            'locked_at' => 'date',
            'option_labels' => 'required|array|between:2,100'
        ];
        foreach($option_labels as $key => $val) {
            $rules['option_labels.'.$key] = 'required';
            $rules['option_colors.'.$key] = ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i'];
        }

        return $rules;
    }
}
