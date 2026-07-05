<?php

namespace App\Http\Requests\Election;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateVoterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['validated', 'banned', 'pending'])],
        ];
    }
}
