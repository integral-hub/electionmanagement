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
                    ->where('election_id', $election->id),
            ],

            'votes.*.candidate_id' => [
                'required',
                'integer',
                Rule::exists('candidates', 'id')
                    ->where('election_id', $election->id),
            ],
        ];
    }
}