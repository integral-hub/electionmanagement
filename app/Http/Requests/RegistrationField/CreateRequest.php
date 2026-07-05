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
        return true;
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
            'fields.*.max_input' => ['nullable', 'integer', 'min:1', 'max:255'],
            'fields.*.options' => ['nullable', 'array'],
            'fields.*.sort_order' => ['nullable', 'integer'],
        ];
    }
    protected function prepareForValidation(): void
    {
        $fields = $this->input('fields', []);

        foreach ($fields as &$field) {
            if (!empty($field['options']) && is_string($field['options'])) {
                $field['options'] = array_filter(
                    array_map('trim', explode(',', $field['options']))
                );
            }
        }

        $this->merge([
            'fields' => $fields,
        ]);
    }
}
