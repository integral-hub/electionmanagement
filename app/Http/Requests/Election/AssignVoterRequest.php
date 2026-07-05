<?php

namespace App\Http\Requests\Election;

use Illuminate\Foundation\Http\FormRequest;

class AssignVoterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'voters'   => ['required', 'array', 'min:1'],
            'voters.*' => ['exists:voters,id'],
            'validate' => ['nullable', 'boolean'], // checkbox validate
        ];
    }

    public function messages(): array
    {
        return [
            'voters.required' => 'Select at least one voter to assign.',
            'voters.min'      => 'Select at least one voter to assign.',
        ];
    }
}
