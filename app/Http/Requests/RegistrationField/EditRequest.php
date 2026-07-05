<?php

namespace App\Http\Requests\RegistrationField;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'fields' => ['sometimes', 'array'],

            'fields.*.label' => ['sometimes', 'string', 'max:25'],
            'fields.*.field_name' => ['sometimes', 'string', 'alpha_dash', 'max:25'],
            'fields.*.description' => ['nullable', 'string', 'max:255'],
            'fields.*.field_type' => ['sometimes', 'string', 'in:text,textarea,select,checkbox,radio,date,file'],
            'fields.*.required' => ['sometimes', 'boolean'],
            'fields.*.unique_field' => ['sometimes', 'boolean'],
            'fields.*.options' => ['nullable', 'array'],
            'fields.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}