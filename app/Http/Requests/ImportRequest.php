<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please upload a voter file.',
            'file.mimes'    => 'Only CSV, XLSX, and XLS files are allowed.',
            'file.max'      => 'The uploaded file must not exceed 10MB.',
        ];
    }
}
