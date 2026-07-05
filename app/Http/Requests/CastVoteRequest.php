<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CastVoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $election = $this->route('election');

        return [
            'votes' => ['required', 'array'],

            'votes.*.position_id' => [
                'required',
                'integer',
                Rule::exists('positions', 'id')
                    ->where('election_id', optional($election)->id),
            ],

            'votes.*.candidate_id' => [
                'required',
                'integer',
                Rule::exists('candidates', 'id')
                    ->where('election_id', optional($election)->id),
            ],
        ];
    }

    /*
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $election = $this->route('election');

            if (!$election || !is_array($this->votes)) {
                return;
            }

            $requiredPositions = $election->positions()
                ->pluck('id')
                ->toArray();

            $submittedPositions = collect($this->votes)
                ->pluck('position_id')
                ->filter()
                ->unique()
                ->toArray();

            $missing = array_diff($requiredPositions, $submittedPositions);

            if (!empty($missing)) {
                $validator->errors()->add(
                    'votes',
                    'You must select a candidate for all positions in this election.'
                );
            }
        });
    } */

    public function messages(): array
    {
        return [
            'votes.required' => 'Please select at least one candidate.',
            'votes.array' => 'Invalid vote submission.',

            'votes.*.position_id.required' => 'A position is missing.',
            'votes.*.position_id.exists' => 'The selected position is invalid.',

            'votes.*.candidate_id.required' => 'Please select a candidate for every position.',
            'votes.*.candidate_id.exists' => 'One of the selected candidates is invalid.',
        ];
    }
}