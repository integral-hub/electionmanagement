<?php

namespace App\Http\Requests\Voter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', Rule::unique('voters', 'phone')->ignore($this->route('voter'))],
            'password' => ['nullable', 'string', 'min:8']
        ];
    }
}