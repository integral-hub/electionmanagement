<?php

namespace App\Http\Requests\RegistrationField;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'fields' => ['required', 'array'],
            
            'fields.*.label' => ['required', 'string', 'max:25'],
            'fields.*.field_name' => ['required', 'string', 'alpha_dash', 'max:25'],
            'fields.*.description' => ['nullable', 'string', 'max:255'],
            'fields.*.field_type' => ['required', 'string', 'in:text,textarea,select,checkbox,radio,date,file'],
            'fields.*.required' => ['required', 'boolean'],
            'fields.*.unique_field' => ['required', 'boolean'],
            'fields.*.is_hash' => ['required', 'boolean'],
            'fields.*.options' => ['nullable', 'array'],
            'fields.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}
