<?php

namespace App\Http\Requests\Position;

class EditRequest extends CreateRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['title'] = ['sometimes', 'string', 'max:255'];

        return $rules;
    }
}