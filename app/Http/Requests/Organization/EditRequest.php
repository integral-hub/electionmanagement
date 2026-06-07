<?php

namespace App\Http\Requests\Organization;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                Rule::unique('organizations', 'email')
                    ->ignore($this->route('organization')),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url'],
            'logo' => ['nullable', 'image'],
            'active' => ['boolean'],
        ];
    }

    /**
     * Normalize payload for Action layer
     */
    public function payload(): array
    {
        return $this->validated();
    }
}