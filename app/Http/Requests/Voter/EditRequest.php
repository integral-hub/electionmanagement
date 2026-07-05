<?php

namespace App\Http\Requests\Voter;

use Illuminate\Validation\Rule;

class EditRequest extends CreateRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $voter = $this->route('voter'); 

        $rules['email'] = [
            'sometimes',
            'email:rfc',
            Rule::unique('voters', 'email')->ignore($voter->id),
        ];

        $rules['phone'] = [
            'nullable',
            'string',
            'max:20',
            Rule::unique('voters', 'phone')->ignore($voter->id),
        ];

        unset($rules['password']); // optional for edit

        return $rules;
    }
}