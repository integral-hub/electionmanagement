<?php

namespace App\Http\Requests\Candidate;

class EditRequest extends CreateRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['position_id'] = ['nullable', 'integer', 'exists:positions,id'];
        $rules['name'] = ['sometimes', 'string', 'max:255'];

        return $rules;
    }
}