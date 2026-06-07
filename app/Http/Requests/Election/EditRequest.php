<?php

namespace App\Http\Requests\Election;

class EditRequest extends CreateRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['name'] = ['nullable', 'string', 'max:255'];

        return $rules;
    }
}